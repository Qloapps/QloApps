<?php
namespace PHPSQLParser\builders;

/**
 * This class implements the builder for the whole UNION ALL statement. You can overwrite
 * all functions to achieve another handling.
 *
 * @author  George Schneeloch <george_schneeloch@hms.harvard.edu>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class UnionAllStatementBuilder implements Builder {



	public function build(array $parsed)
	{
		$sql = '';
		$select_builder = new SelectStatementBuilder();
		$first = true;
		foreach ($parsed['UNION ALL'] as $clause) {
			if (!$first) {
				$sql .= " UNION ALL ";
			}
			else {
				$first = false;
			}

			$sql .= $select_builder->build($clause);
		}
		return $sql;
	}
}