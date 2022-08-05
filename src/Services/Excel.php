<?php

namespace Volt\Services;

class Excel
{
    private $excel_src;

    public function __construct(string $excel_src)
    {
        $this->setFileSrc($excel_src);
    }

    public function setFileSrc(string $excel_src)
    {
        $this->excel_src = $excel_src;
    }

    public function getAllData(): array
    {
        // https://www.codegrepper.com/code-examples/php/reading+and+displaying+data+from+a+csv+file+in+php+using+fopen
        $row = 1;
        $output = [];
        if (($handle = fopen($this->excel_src, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $num = count($data);
                $row++;
                for ($c = 0; $c < $num; $c++) {
                    $output[] = explode(",", $data[$c]);
                }
            }
            fclose($handle);
        }

        return $output;
    }
}
