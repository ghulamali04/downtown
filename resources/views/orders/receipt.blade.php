<?php
$receipt = "Store Name\n";
$receipt .= "123 Business St, City\n";
$receipt .= "Tel: (123) 456-7890\n\n";
$receipt .= "--------------------------------\n";
$receipt .= "Item          Qty   Price   Total\n";
$receipt .= "--------------------------------\n";
$receipt .= "Product A      2   10.00   20.00\n";
$receipt .= "Product B      1   15.00   15.00\n";
$receipt .= "--------------------------------\n";
$receipt .= "Total:               36.75\n";
$receipt .= "--------------------------------\n\n";
$receipt .= "Thank you for shopping!\n";

// Send the receipt to the printer
file_put_contents(public_path("receipt.txt"), $receipt);
exec("lp -d STMicroelectronics_USB_Printer " . public_path("receipt.txt"));

echo "Receipt sent to printer.";

?>
