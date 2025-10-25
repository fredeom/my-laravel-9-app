<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Dompdf\Options;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Validator;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory as PhpWordIOFactory;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;

class OrderController extends Controller
{
    public function index() {
        $orders = Order::orderBy("created_at","desc")->get();
        return view("orders.index", compact("orders"));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'fio' => 'required|min:3',
            'product_id' => 'required|exists:products,id',
            'comment' => 'nullable',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            $products = Product::all();

            $errors = new ViewErrorBag();
            $errors->put('default', $validator->errors());

            return view('/orders/new', compact(['request', 'products', 'errors']));
        }

        $validated = $validator->validated();
        $validated['status'] = Order::NEW;

        Order::create($validated);

        return view('orders.success', ['message' => 'Заказ успешно создан!']);
    }

    public function done($id) {
        $order = Order::findOrFail($id);
        $order->status = Order::DONE;
        $order->save();
        return view('orders.success', ['message' => 'Выполнен. Статус успешно изменён!']);
    }

    public function exportWord():BinaryFileResponse {
        $tempFile = tempnam(sys_get_temp_dir(), 'download_');

        $orders = DB::table('orders')
                ->join('products', 'orders.product_id', '=', 'products.id')
                ->select('orders.id', 'orders.created_at', 'orders.fio', 'orders.status', 'orders.amount',
                'orders.comment', 'products.name', 'products.price', DB::raw('products.price * orders.amount AS total_price'))
                ->get()
                ->toArray();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addImage(
            storage_path('app/public/images/logo.png'),
            ['width' => 100]
        );

        $section->addText("Отчёт о заказах", ['size'=> '20', 'color' => 'red', 'bgColor' => 'f0f000']);
        $section->addText("", ['size'=> '20', 'fgColor' => 'red', 'bgColor' => 'f0f000']);

        $section->addText("Выполненные заказы", ['size'=> '12', 'bgColor' => 'f7f707']);
        $section->addText("", ['size'=> '12', 'bgColor' => 'f7f707']);

        $table = $section->addTable();
        $table->addRow();
        foreach (['Номер заказа', 'Дата создания', 'ФИО покупателя', 'Статус заказа', 'Кол-во', 'Коммент',
            'Наименование продукта', 'Цена за штуку', 'Итоговая цена, ₽'] as $title) {
            $table->addCell(null, ['bgColor' => 'E0E0E0'])->addText($title,
            ['bold' => true], ['align' => 'center']);
        }
        foreach ($orders as $row) {
            if ($row->status == 'Выполнен') {
                $table->addRow();
                foreach ($row as $cellValue) {
                    $table->addCell()->addText($cellValue);
                }
            }
        }

        $section->addText("", ['size'=> '12', 'bgColor' => '07f7f7']);
        $section->addText("НЕ выполненные заказы", ['size'=> '12', 'bgColor' => '07f7f7']);
        $section->addText("", ['size'=> '12', 'bgColor' => '07f7f7']);

        $table = $section->addTable();
        $table->addRow();
        foreach (['Номер заказа', 'Дата создания', 'ФИО покупателя', 'Статус заказа', 'Кол-во', 'Коммент',
            'Наименование продукта', 'Цена за штуку', 'Итоговая цена, ₽'] as $title) {
            $table->addCell(null, ['bgColor' => 'E0E0E0'])->addText($title,
            ['bold' => true], ['align' => 'center']);
        }
        foreach ($orders as $row) {
            if ($row->status != 'Выполнен') {
                $table->addRow();
                foreach ($row as $cellValue) {
                    $table->addCell()->addText($cellValue);
                }
            }
        }

        $writer = PhpWordIOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, "Заказы_" . date("Y-m-d His"). "_.docx")->deleteFileAfterSend(true);
    }

    public function exportPdf():BinaryFileResponse {
        $tempFile = tempnam(sys_get_temp_dir(), 'download_');

        $orders = DB::table('orders')
                ->join('products', 'orders.product_id', '=', 'products.id')
                ->select('orders.id', 'orders.created_at', 'orders.fio', 'orders.status', 'orders.amount',
                'orders.comment', 'products.name', 'products.price', DB::raw('products.price * orders.amount AS total_price'))
                ->get()
                ->toArray();


        $html = '
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <title>Orders Export</title>
            <style>
                body {
                    font-family: DejaVu Sans, sans-serif;
                    font-size: 12px;
                }
                h1 { color: #333; text-align: center; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                .total-row { background-color: #e9ecef !important; font-weight: bold; }
                .text-right { text-align: right; }
                .date { color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <h1>Отчёт выполненных заказов</h1>
            <p class=\"date\">Создан : ' . date('Y-m-d H:i:s') . "</p>
            <table>
                <thead>
                    <tr>";
        foreach (['Номер заказа', 'Дата создания', 'ФИО покупателя', 'Статус заказа', 'Кол-во', 'Коммент',
            'Наименование продукта', 'Цена за штуку', 'Итоговая цена, ₽'] as $title) {
            $html .= "<th>$title</th>";
        }
        $html .=  "</tr>
                </thead>
                <tbody>";

        $totalPrice = 0;
        $cntDone = 0;
        foreach ($orders as $order) {
            if ($order->status == 'Выполнен') {
                $html .= '
                        <tr>
                            <td>' . htmlspecialchars($order->id) . '</td>
                            <td>' . htmlspecialchars($order->created_at) . '</td>
                            <td>' . htmlspecialchars($order->fio) . '</td>
                            <td>' . htmlspecialchars($order->status) . '</td>
                            <td>' . htmlspecialchars($order->amount) . '</td>
                            <td>' . htmlspecialchars($order->comment) . '</td>
                            <td>' . htmlspecialchars($order->name) . '</td>
                            <td>' . htmlspecialchars($order->price) . '</td>
                            <td class="text-right">$' . number_format($order->total_price, 2) . '</td>
                        </tr>';
                $totalPrice += $order->total_price;
                $cntDone++;
            }
        }

        $html .= '
                    <tr class="total-row">
                        <td colspan="8" class="text-right"><strong>Grand Total:</strong></td>
                        <td class="text-right"><strong>$' . number_format($totalPrice, 2) . '</strong></td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top: 30px; color: #666; font-size: 12px;">
                Всего выполненных заказов: ' . $cntDone . '
            </p>';

        $html .= '<h1>Отчёт Не выполненных заказов</h1>
            <p class=\"date\">Создан : ' . date('Y-m-d H:i:s') . "</p>
            <table>
                <thead>
                    <tr>";
        foreach (['Номер заказа', 'Дата создания', 'ФИО покупателя', 'Статус заказа', 'Кол-во', 'Коммент',
            'Наименование продукта', 'Цена за штуку', 'Итоговая цена, ₽'] as $title) {
            $html .= "<th>$title</th>";
        }
        $html .=  "</tr>
                </thead>
                <tbody>";

        $totalPrice = 0;
        $cntUnDone = 0;
        foreach ($orders as $order) {
            if ($order->status != 'Выполнен') {
                $html .= '
                        <tr>
                            <td>' . htmlspecialchars($order->id) . '</td>
                            <td>' . htmlspecialchars($order->created_at) . '</td>
                            <td>' . htmlspecialchars($order->fio) . '</td>
                            <td>' . htmlspecialchars($order->status) . '</td>
                            <td>' . htmlspecialchars($order->amount) . '</td>
                            <td>' . htmlspecialchars($order->comment) . '</td>
                            <td>' . htmlspecialchars($order->name) . '</td>
                            <td>' . htmlspecialchars($order->price) . '</td>
                            <td class="text-right">$' . number_format($order->total_price, 2) . '</td>
                        </tr>';
                $totalPrice += $order->total_price;
                $cntUnDone++;
            }
        }

        $html .= '
                    <tr class="total-row">
                        <td colspan="8" class="text-right"><strong>Grand Total:</strong></td>
                        <td class="text-right"><strong>$' . number_format($totalPrice, 2) . '</strong></td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top: 30px; color: #666; font-size: 12px;">
                Всего не выполненных заказов: ' . $cntUnDone . '
            </p>
        </body>
        </html>';


        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans'); // Supports Cyrillic
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents($tempFile, $output);

        return response()->download($tempFile, "Заказы_" . date("Y-m-d His"). "_.pdf")->deleteFileAfterSend(true);
    }
}
