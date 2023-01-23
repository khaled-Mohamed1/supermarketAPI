<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    /**
     * @param array $row
     * @return Product
     */
    public function model(array $row): Product
    {

        return new Product([
            "category_id"=> 1,
            "product_name"=>$row['product_name'],
            "product_description"=> 'Target Keyword',
            "product_image"=>'http://node.tojar-gaza.com/storage/app/public/products/n7fTBkTOLeU4t3GczfXKN25AcqpJjVXd.png',
            "product_quantity"=>10000,
            "product_price"=>$row['product_price'],
        ]);

    }

    public function getCsvSettings(): array
    {
        # Define your custom import settings for only this class
        return [
            'input_encoding' => 'UTF-8',
        ];
    }
}
