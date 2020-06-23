<?php
declare(strict_types=1);

namespace SimpleSAML\Module\scoutnetmodule\Auth\Process;

class AuthLog extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * @param array $request
     * @return mixed
     * @throws JsonException
     */
    public function process(&$request)
    {
        $ip = $_SERVER['HTTP_X_REAL_IP'] ?: $_SERVER['HTTP_X_FORWARDED_FOR'] ?: $_SERVER['REMOTE_ADDR'];
        if(!empty($request['Attributes']['sub'][0])) {
            $subject = $request['Attributes']['sub'][0];
        } elseif($request['Attributes']['uid'][0]) {
            $subject = $request['Attributes']['uid'][0];
        } else {
            $subject = 'ghost';
        }
        $params = [
            'sub' => $subject,
            'entity_id' => $request['SPMetadata']['entityid'],
            'auth_type' => 'SAML2',
            'ip' => $ip,
        ];
        $query = <<<'SQL'
        INSERT INTO authentictions_log (sub, entity_id, auth_type, ip)
        SELECT input.* FROM (
            SELECT
                :sub AS sub,
                :entity_id AS entity_id,
                :auth_type AS auth_type,
                :ip AS ip
            ) AS input
        ON DUPLICATE KEY UPDATE
            refreched_at = NOW()
        SQL;
        \SimpleSAML\Database::getInstance()->read($query, $params);
    }
}
