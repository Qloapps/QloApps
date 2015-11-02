<?php

class AverageTaxOfProductsTaxCalculator
{
    private $id_order;
    private $configuration;
    private $db;

    public $computation_method = 'average_tax_of_products';

    public function __construct(Core_Foundation_Database_DatabaseInterface $db, Core_Business_ConfigurationInterface $configuration)
    {
        $this->db = $db;
        $this->configuration = $configuration;
    }

    private function getProductTaxes()
    {
        $prefix = $this->configuration->get('_DB_PREFIX_');

        $sql = 'SELECT t.id_tax, t.rate, od.total_price_tax_excl FROM '.$prefix.'orders o
                INNER JOIN '.$prefix.'order_detail od ON od.id_order = o.id_order
                INNER JOIN '.$prefix.'order_detail_tax odt ON odt.id_order_detail = od.id_order_detail
                INNER JOIN '.$prefix.'tax t ON t.id_tax = odt.id_tax
                WHERE o.id_order = '.(int)$this->id_order;

        return $this->db->select($sql);
    }

    public function setIdOrder($id_order)
    {
        $this->id_order = $id_order;
        return $this;
    }

    public function getTaxesAmount($price_before_tax, $price_after_tax = null, $round_precision = 2, $round_mode = null)
    {
        $amounts = array();
        $total_base = 0;

        foreach ($this->getProductTaxes() as $row) {
            if (!array_key_exists($row['id_tax'], $amounts)) {
                $amounts[$row['id_tax']] = array(
                    'rate' => $row['rate'],
                    'base' => 0
                );
            }

            $amounts[$row['id_tax']]['base'] += $row['total_price_tax_excl'];
            $total_base += $row['total_price_tax_excl'];
        }

        $actual_tax = 0;
        foreach ($amounts as &$data) {
            $data = Tools::ps_round(
                $price_before_tax * ($data['base'] / $total_base) * $data['rate'] / 100,
                $round_precision,
                $round_mode
            );
            $actual_tax += $data;
        }
        unset($data);

        if ($price_after_tax) {
            Tools::spreadAmount(
                $price_after_tax - $price_before_tax - $actual_tax,
                $round_precision,
                $amounts,
                'id_tax'
            );
        }

        return $amounts;
    }
}
