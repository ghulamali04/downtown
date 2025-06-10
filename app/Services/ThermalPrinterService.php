<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

class ThermalPrinterService
{
    private $printerIp;
    private $printerPort;

    public function __construct($printerIp = '192.168.1.101', $printerPort = 9100)
    {
        $this->printerIp = $printerIp;
        $this->printerPort = $printerPort;
    }

    /**
     * Print receipt for restaurant order
     */
    public function printReceipt($orderData)
{
    if (isset($orderData->stdClass)) {
        $order = $orderData->stdClass;
    } else {
        $order = $orderData;
    }

    try {
        // Connect to thermal printer via network
        $connector = new NetworkPrintConnector($this->printerIp, $this->printerPort);
        $printer = new Printer($connector);

        $printer->initialize();
        usleep(500000);

        // Header Section
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
        $printer->setEmphasis(true);
        $printer->text("DOWNTOWN\n");
        $printer->selectPrintMode();
        $printer->text("BAHAWALNAGAR\n");
        $printer->setEmphasis(false);
        $printer->text("Tel: (063) 2280-988\n");
        $printer->text("Phone: 03202280987\n");
        $printer->text("03132890988\n");
        $printer->text((@$order["is_paid"] == 1 ? 'PAID' : 'UNPAID') . "\n");
        $printer->feed(1);

        // Token and Order Info
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $lineWidth = 44;
        $orderIdText = "ORDER ID: " . @$order["id"];
        $orderTypeText = "Order Type: " . ucfirst(@$order["type"]);
        $spacesNeeded = $lineWidth - strlen($orderIdText) - strlen($orderTypeText);
        $spaces = str_repeat(" ", max(1, $spacesNeeded));
        $printer->text($orderIdText . $spaces);
        $printer->setEmphasis(true);
        $printer->text($orderTypeText . "\n");
        $printer->setEmphasis(false);
        $printer->text("Date: " . now()->format('d/m/Y H:i') . "\n");
        $printer->text("User: " . @$order["user"]["first_name"] . ' ' . @$order["user"]["last_name"] . "\n");
        $printer->feed(1);

        // Order Details Header
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text(str_repeat("-", 44) . "\n");
        $printer->text("Order Detail\n");
        $printer->text(str_repeat("-", 44) . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        // Order Items
        $total = 0;
        $printer->text(sprintf("%-20s %-5s %-8s %8s\n", "Item", "Qty", "Rate", "Total"));
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text(str_repeat("-", 44) . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
if (@$order["items"]) {
        foreach (@$order["items"] as $item) {
            $itemTotal = @$item["price"] * @$item["qty"];
            $total += $itemTotal;

            // Item name on one line
            $printer->text(@$item["name"] . "\n");

            // Quantity, Rate, and Total on the next line with right-aligned amounts
            $printer->text(sprintf("%-20s %-5s %-8s %8s\n", "", @$item["qty"], number_format(@$item["price"], 2), number_format($itemTotal, 2)));
            $printer->feed();
        }

    }

        // Subtotal, VAT/GST, and Grand Total
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text(str_repeat("-", 44) . "\n");
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
        $printer->text(str_repeat("-", 44) . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text(@$order["customer"]["phone_number"]. "\n");
        $printer->text("Delivery Address: " . @$order["customer"]["address"] . "\n");
        $printer->text("Order-Taker: " . @$order["user"]["first_name"] . ' ' . @$order["user"]["last_name"] . "\n");
        $printer->feed(1);

        // Footer
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text(str_repeat("-", 44) . "\n");
        $printer->text("Printed: " . now()->format('d/m/Y H:i') . "\n");
        $printer->text("FOR ANY COMPLAINT & SUGGESTIONS\n");
        $printer->text("PLEASE CONTACT US @ (063) 2280-988\n");
        $printer->text("Software By Bitzsol\n");
        $printer->feed(3);

        // Cut paper
        $printer->cut(Printer::CUT_PARTIAL);
        usleep(200000);
        $printer->close();

        return ['success' => true, 'message' => 'Receipt printed successfully'];

    } catch (\Exception $e) {
        return ['success' => false, 'message' => 'Print error: ' . $e->getMessage()];
    }
}

    /**
     * Print kitchen order
     */
    public function printKitchenOrder($orderData)
    {
        try {
            $connector = new NetworkPrintConnector($this->printerIp, $this->printerPort);
            $printer = new Printer($connector);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("KITCHEN ORDER");
            $printer->selectPrintMode();
            $printer->feed(2);

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Order #: " . $orderData['order_id']);
            $printer->feed();
            $printer->text("Time: " . date('H:i:s'));
            $printer->feed();

            if (isset($orderData['table'])) {
                $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $printer->text("TABLE: " . $orderData['table']);
                $printer->selectPrintMode();
                $printer->feed(2);
            }

            $printer->text(str_repeat('=', 32));
            $printer->feed();

            foreach ($orderData['items'] as $item) {
                $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
                $printer->text($item['quantity'] . "x " . $item['name']);
                $printer->selectPrintMode();
                $printer->feed();

                if (!empty($item['notes'])) {
                    $printer->text(">>> " . $item['notes'] . " <<<");
                    $printer->feed();
                }
                $printer->feed();
            }

            $printer->text(str_repeat('=', 32));
            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return ['success' => true, 'message' => 'Kitchen order printed successfully'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Print error: ' . $e->getMessage()];
        }
    }
}
