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
    public function printReceipt($orderData)
    {
        try {
            // Connect to thermal printer via network
            $connector = new NetworkPrintConnector($this->printerIp, $this->printerPort);
            $printer = new Printer($connector);

            // Restaurant header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text($orderData['restaurant_name'] ?? 'RESTAURANT NAME');
            $printer->feed();

            $printer->selectPrintMode();
            $printer->text($orderData['address'] ?? 'Restaurant Address');
            $printer->feed();
            $printer->text("Tel: " . ($orderData['phone'] ?? '000-000-0000'));
            $printer->feed(2);

            // Order details
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Order #: " . $orderData['order_id']);
            $printer->feed();
            $printer->text("Date: " . date('Y-m-d H:i:s'));
            $printer->feed();

            if (isset($orderData['table'])) {
                $printer->text("Table: " . $orderData['table']);
                $printer->feed();
            }

            if (isset($orderData['customer'])) {
                $printer->text("Customer: " . $orderData['customer']);
                $printer->feed();
            }

            $printer->feed();
            $printer->text(str_repeat('-', 32));
            $printer->feed();

            // Items
            $total = 0;
            foreach ($orderData['items'] as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                $total += $itemTotal;

                $printer->text($item['name']);
                $printer->feed();
                $printer->text(sprintf("%dx%.2f%s%.2f",
                    $item['quantity'],
                    $item['price'],
                    str_repeat(' ', 20 - strlen($item['quantity'] . 'x' . number_format($item['price'], 2))),
                    $itemTotal
                ));
                $printer->feed();

                // Special instructions
                if (!empty($item['notes'])) {
                    $printer->text("  Note: " . $item['notes']);
                    $printer->feed();
                }
            }

            $printer->text(str_repeat('-', 32));
            $printer->feed();

            // Totals
            if (isset($orderData['subtotal'])) {
                $printer->text(sprintf("Subtotal:%s%.2f",
                    str_repeat(' ', 21 - strlen('Subtotal:')),
                    $orderData['subtotal']
                ));
                $printer->feed();
            }

            if (isset($orderData['tax'])) {
                $printer->text(sprintf("Tax:%s%.2f",
                    str_repeat(' ', 25 - strlen('Tax:')),
                    $orderData['tax']
                ));
                $printer->feed();
            }

            if (isset($orderData['discount'])) {
                $printer->text(sprintf("Discount:%s-%.2f",
                    str_repeat(' ', 20 - strlen('Discount:')),
                    $orderData['discount']
                ));
                $printer->feed();
            }

            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text(sprintf("TOTAL:%s%.2f",
                str_repeat(' ', 15 - strlen('TOTAL:')),
                $total
            ));
            $printer->selectPrintMode();
            $printer->feed(2);

            // Payment info
            if (isset($orderData['payment_method'])) {
                $printer->text("Payment: " . $orderData['payment_method']);
                $printer->feed();
            }

            // Footer
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->feed();
            $printer->text("Thank you for dining with us!");
            $printer->feed(2);

            // Cut paper
            $printer->cut();
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
