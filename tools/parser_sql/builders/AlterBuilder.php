<?php
namespace PHPSQLParser\builders;

/**
 * This class implements the builder for the [DELETE] part. You can overwrite
 * all functions to achieve another handling.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class AlterBuilder implements Builder
{
    public function build(array $parsed)
    {
        $sql = '';

        foreach ($parsed as $term) {
            if ($term === ' ') {
                continue;
            }

            if (substr($term, 0, 1) === '(' ||
                strpos($term, "\n") !== false) {
                $sql = rtrim($sql);
            }

            $sql .= $term . ' ';
        }

        $sql = rtrim($sql);

        return $sql;
    }
}
