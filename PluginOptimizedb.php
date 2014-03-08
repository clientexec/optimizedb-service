<?php
require_once 'modules/admin/models/ServicePlugin.php';
/**
* @package Plugins
*/
class PluginOptimizedb extends ServicePlugin
{
    protected $featureSet = 'restricted';
    public $hasPendingItems = false;

    function getVariables()
    {
        $variables = array(
            /*T*/'Plugin Name'/*/T*/   => array(
                'type'          => 'hidden',
                'description'   => /*T*/''/*/T*/,
                'value'         => /*T*/'Optimize DataBase'/*/T*/,
            ),
            /*T*/'Enabled'/*/T*/       => array(
                'type'          => 'yesno',
                'description'   => /*T*/'When enabled, this service will optimize all tables in your ClientExec database.'/*/T*/,
                'value'         => '0',
            ),
            /*T*/'Run schedule - Minute'/*/T*/  => array(
                'type'          => 'text',
                'description'   => /*T*/'Enter number, range, list or steps'/*/T*/,
                'value'         => '30',
                'helpid'        => '8',
            ),
            /*T*/'Run schedule - Hour'/*/T*/  => array(
                'type'          => 'text',
                'description'   => /*T*/'Enter number, range, list or steps'/*/T*/,
                'value'         => '01',
            ),
            /*T*/'Run schedule - Day'/*/T*/  => array(
                'type'          => 'text',
                'description'   => /*T*/'Enter number, range, list or steps'/*/T*/,
                'value'         => '15',
            ),
            /*T*/'Run schedule - Month'/*/T*/  => array(
                'type'          => 'text',
                'description'   => /*T*/'Enter number, range, list or steps'/*/T*/,
                'value'         => '*',
            ),
            /*T*/'Run schedule - Day of the week'/*/T*/  => array(
                'type'          => 'text',
                'description'   => /*T*/'Enter number in range 0-6 (0 is Sunday) or a 3 letter shortcut (e.g. sun)'/*/T*/,
                'value'         => '*',
            ),
        );

        return $variables;
    }

    function execute()
    {
        @set_time_limit(0);

        $toOptimize = $this->getTablesToOptimize();
        if ( count ( $toOptimize ) > 0 ) {
            foreach ( $toOptimize as $table ) {
                $this->db->query("OPTIMIZE TABLE `{$table}`");
            }
        }
    }

    function getTablesToOptimize()
    {
        $configuration = Zend_Registry::get('configuration');
        $database = $this->db->escape_string($configuration['application']['dbSchema']);
        // We need to remove Engine='MyISAM' when we can see how innodb treats fragmented tables.
        $result = $this->db->query("SHOW TABLE STATUS FROM `{$database}` WHERE Data_free>0 AND Engine='MyISAM'");
        $toOptimize = array();
        while ( $row = $result->fetch() ) {
            $toOptimize [] = $row['Name'];
        }
        return $toOptimize;
    }
}