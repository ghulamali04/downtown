<?php

namespace App\Services;

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

class ThermalPrinterService
{
    private $printerIp;
    private $printerPort;

    public function __construct($printerIp = '192.168.1.100', $printerPort = 9100)
    {
        $this->printerIp = $printerIp;
        $this->printerPort = $printerPort;
    }

    /**
     * Print receipt for restaurant order
     */
    public function printReceipt($order): object
    {
        try {
            // Connect to thermal printer via network
            $connector = new NetworkPrintConnector($this->printerIp, $this->printerPort);
            $printer = new Printer($connector);

            // Restaurant header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->feed(2);
            // Header Section
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("DOWNTOWN\n");
            $printer->text("BAHAWALNAGAR\n");
            $printer->setEmphasis(false);
            $printer->text("Phone: 03202280987\n");
            $printer->text("03132890988\n");
            $printer->text("".$order->is_paid == 1 ? 'PAID' : 'UNPAID'."\n");
            $printer->feed(1);

            // Token and Order Info
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $lineWidth = 42;
            $orderIdText = "ORDER ID: " . $order->id;
            $orderTypeText = "Order Type: " . ucfirst($order->type);
            $spacesNeeded = $lineWidth - strlen($orderIdText) - strlen($orderTypeText);
            $spaces = str_repeat(" ", max(1, $spacesNeeded));
            $printer->text($orderIdText . $spaces);
            $printer->setEmphasis(true);
            $printer->text($orderTypeText . "\n");
            $printer->setEmphasis(false);
            $printer->text("Date: " . now()->format('d/m/Y H:i') . "\n");
            $printer->text("User: " . $order->user->first_name . ' ' . $order->user->last_name . "\n");
            $printer->feed(1);

            // Order Details Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text(str_repeat("-", 42) . "\n");
            $printer->text("Order Detail\n");
            $printer->text(str_repeat("-", 42) . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);

            // Order Items
            $total = 0;
            $printer->text(sprintf("%-20s %-5s %-8s %8s\n", "Item", "Qty", "Rate", "Total"));
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text(str_repeat("-", 42) . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            foreach ($order->items as $item) {
                $itemTotal = $item->price * $item->qty;
                $total += $itemTotal;

                // Item name on one line
                $printer->text($item->name . "\n");

                // Quantity, Rate, and Total on the next line with right-aligned amounts
                $printer->text(sprintf("%-20s %-5s %-8s %8s\n", "", $item->qty, number_format($item->price, 2), number_format($itemTotal, 2)));
                $printer->feed();
            }

            // Subtotal, VAT/GST, and Grand Total
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text(str_repeat("-", 42) . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $subTotalText = "Sub Total";
            $subTotalValue = number_format($total, 2);
            $printer->text(sprintf("%-30s %8s Rs\n", $subTotalText, $subTotalValue));

            $vatText = "VAT/GST (0% on Cash)";
            $vatValue = "0.00";
            $printer->text(sprintf("%-30s %8s Rs\n", $vatText, $vatValue));

            $grandTotalText = "GRAND TOTAL";
            $grandTotalValue = number_format($total, 2);
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-30s %8s Rs\n", $grandTotalText, $grandTotalValue));
            $printer->setEmphasis(false);
            $printer->feed(1);

            // Customer Details
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Customer Detail\n");
            $printer->text(str_repeat("-", 42) . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("".$order->customer->phone_number."\n");
            $printer->text("Delivery Address: ".$order->customer->address."\n");
            $printer->text("Order-Taker: ".$order->user->first_name.' '.$order->user->last_name."\n");
            $printer->feed(1);

            // Footer
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text(str_repeat("-", 42) . "\n");
            $printer->text("Printed: " . now()->format('d/m/Y H:i') . "\n");
            $printer->text("FOR ANY COMPLAINT & SUGGESTIONS\n");
            $printer->text("PLEASE CONTACT US @ (063) 2280-988\n");
            $printer->text("Software By Bitzsol\n");
            $printer->feed(3);

            // Finalize
            $printer->cut();
            $printer->close();

            return (object)['success' => true, 'message' => 'Receipt printed successfully'];

        } catch (\Exception $e) {
            return (object)['success' => false, 'message' => 'Print error: ' . $e->getMessage()];
        }
    }
}
