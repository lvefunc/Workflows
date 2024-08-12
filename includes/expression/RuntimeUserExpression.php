<?php

namespace Workflows\Expression;

use MediaWiki\MediaWikiServices;
use MWException;
use User;
use Wikimedia\Rdbms\SelectQueryBuilder;
use Workflows\Enumeration\RuntimeUserExpressionType;
use Workflows\Runtime\Context\Context;

/**
 * @Table(name: "wfs_expr_runtime_user")
 * @BaseEntity(name: "Workflows\Expression\Expression")
 */
final class RuntimeUserExpression extends UserExpression {
    /**
     * @Column(name: "type", type: "varbinary", length: 255, nullable: false)
     */
    private string $type;

    public function __construct( string $type ) {
        parent::__construct();
        $this->setType( $type );
    }

    /**
     * @throws MWException
     */
    public function evaluate( Context $context ) : User {
        switch ( $this->getType() ) {
            case RuntimeUserExpressionType::Owner:
                return $context->getWorkflow()->getOwner();
            case RuntimeUserExpressionType::Random:
                $mwServices = MediaWikiServices::getInstance();
                $dbr = $mwServices->getDBLoadBalancer()->getConnection( DB_REPLICA );

                $rows = $dbr->query(
                    $dbr->limitResult(
                        $dbr->selectSQLText(
                            "user", [
                                "user_id"
                            ], [
                                "user_name != \"MediaWiki default\""
                            ], __METHOD__, [
                                "ORDER BY" => "RAND()"
                        ] ), 1
                    )
                );

                foreach ( $rows as $row ) {
                    return $mwServices->getUserFactory()->newFromId( $row->user_id );
                }

                throw new MWException( "There are no users in this MediaWiki" );
            case RuntimeUserExpressionType::Sysop:
                $mwServices = MediaWikiServices::getInstance();
                $dbr = $mwServices->getDBLoadBalancer()->getConnection( DB_REPLICA );

                $rows = $dbr->newSelectQueryBuilder()
                    ->select( "user_id" )
                    ->from( "user_groups" )
                    ->join( "user", null, "user_groups.ug_user = user.user_id" )
                    ->where( [ "ug_group = \"sysop\"" ] )
                    ->options( [ "ORDER BY" => "RAND()", "LIMIT" => 1 ] )
                    ->caller( __METHOD__ )
                    ->fetchResultSet();

                foreach ( $rows as $row ) {
                    return $mwServices->getUserFactory()->newFromId( $row->user_id );
                }

                throw new MWException( "There are no sysops in this MediaWiki" );
            default:
                throw new MWException( "Invalid runtime user expression type" );
        }
    }

    /**
     * select user_id from user_groups
    inner join user on user.user_id = user_groups.ug_user
    where ug_group = 'sysop' order by rand() limit 1;
     */

    public function getType() : string {
        return $this->type;
    }

    /**
     * @throws MWException
     */
    public function setType( string $type ) : void {
        RuntimeUserExpressionType::verify( $type );
        $this->type = $type;
        $this->markAsDirty();
    }
}