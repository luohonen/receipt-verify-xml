# 全网唯一的windows收据(XML格式)验证器 The only windows receipt (XML format) verifier in the whole network

## 安装

```shell
composer require luohonen/receipt-verify-xml
```

## 基础用法

~~~php
<?php

use Luohonen\Receipt;

//要验证的收据-XML格式 Receipt to verify - XML format
$xml = 'receipt-xml';
try{

   Receipt::verify($xml);

   print_r("success");

}catch (\Exception $e){
   
   print_r($e->getMessage());

}
    
~~~



