<?php
namespace PHPSQLParser\builders;

class AlterStatementBuilder implements Builder
{
    protected function buildSubTree($parsed) {
        $builder = new SubTreeBuilder();
        return $builder->build($parsed);
    }

    private function buildAlter($parsed)
    {
        $builder = new AlterBuilder();
        return $builder->build($parsed);
    }

    public function build(array $parsed)
    {
        $alter = $parsed['ALTER'];
        $sql = $this->buildAlter($alter);

        return $sql;
    }
}
