<?php
// Factura de compra de un artículo: IVA 12% y descuento 15% si el total con IVA supera $150.

const IVA = 0.12;
const LIMITE_DESCUENTO = 150.00;
const DESCUENTO = 0.15;

$errores = [];
$factura = null;
$descripcion = trim($_POST['descripcion'] ?? '');
$cantidad = $_POST['cantidad'] ?? '';
$precio = $_POST['precio'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($descripcion === '') {
        $errores[] = 'Debe ingresar la descripción del artículo.';
    }
    if (!ctype_digit((string)$cantidad) || (int)$cantidad < 1) {
        $errores[] = 'La cantidad debe ser un número entero mayor que 0.';
    }
    if (!is_numeric($precio) || (float)$precio <= 0) {
        $errores[] = 'El precio unitario debe ser un número mayor que 0.';
    }

    if (!$errores) {
        $cantidad = (int)$cantidad;
        $precio = (float)$precio;

        $subtotal = $cantidad * $precio;               // precio bruto (base)
        $valorIva = $subtotal * IVA;                   // IVA 12%
        $totalConIva = $subtotal + $valorIva;          // precio total de venta
        $aplicaDescuento = $totalConIva > LIMITE_DESCUENTO;
        $montoDescuento = $aplicaDescuento ? $totalConIva * DESCUENTO : 0.0;
        $totalNeto = $totalConIva - $montoDescuento;   // monto final a pagar

        $factura = compact(
            'descripcion', 'cantidad', 'precio', 'subtotal', 'valorIva',
            'totalConIva', 'aplicaDescuento', 'montoDescuento', 'totalNeto'
        );
    }
}

function money(float $n): string
{
    return '$ ' . number_format($n, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emisión de factura</title>
    <style>
        body { font-family: Arial, sans-serif; background: #e87d94; margin: 0; padding: 24px; color: #222; }
        .card { max-width: 520px; margin: 0 auto 20px; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        h1, h2 { margin-top: 0; }
        label { display: block; margin-top: 14px; font-weight: bold; }
        input { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; border: 1px solid #bbb; border-radius: 4px; }
        button { margin-top: 18px; padding: 10px 18px; background: #9a1b2a; color: #fff; border: 0; border-radius: 4px; cursor: pointer; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 8px 4px; border-bottom: 1px solid #e3e3e3; }
        td:last-child { text-align: right; }
        .total td { font-weight: bold; font-size: 1.1em; border-bottom: 0; border-top: 2px solid #222; }
        .error { background: #fde8e8; color: #9b1c1c; padding: 10px 14px; border-radius: 4px; margin-top: 14px; }
        .error ul { margin: 0; padding-left: 18px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Emisión de factura</h1>
        <form method="post" action="">
            <label for="descripcion">Descripción del artículo</label>
            <input type="text" id="descripcion" name="descripcion" required
                   value="<?= htmlspecialchars($descripcion) ?>">

            <label for="cantidad">Cantidad adquirida</label>
            <input type="number" id="cantidad" name="cantidad" min="1" step="1" required
                   value="<?= htmlspecialchars((string)$cantidad) ?>">

            <label for="precio">Precio unitario de venta ($)</label>
            <input type="number" id="precio" name="precio" min="0.01" step="0.01" required
                   value="<?= htmlspecialchars((string)$precio) ?>">

            <button type="submit">Emitir factura</button>
        </form>

        <?php if ($errores): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($factura): ?>
    <div class="card">
        <h2>Factura</h2>
        <table>
            <tr><td>Artículo</td><td><?= htmlspecialchars($factura['descripcion']) ?></td></tr>
            <tr><td>Cantidad</td><td><?= $factura['cantidad'] ?></td></tr>
            <tr><td>Precio unitario</td><td><?= money($factura['precio']) ?></td></tr>
            <tr><td>Subtotal (precio bruto)</td><td><?= money($factura['subtotal']) ?></td></tr>
            <tr><td>IVA (12%)</td><td><?= money($factura['valorIva']) ?></td></tr>
            <tr><td>Total con IVA</td><td><?= money($factura['totalConIva']) ?></td></tr>
            <tr>
                <td>Descuento (15%)</td>
                <td>
                    <?php if ($factura['aplicaDescuento']): ?>
                        Sí aplica: - <?= money($factura['montoDescuento']) ?>
                    <?php else: ?>
                        No aplica (el total no supera $ 150,00)
                    <?php endif; ?>
                </td>
            </tr>
            <tr class="total"><td>Total neto a pagar</td><td><?= money($factura['totalNeto']) ?></td></tr>
        </table>
    </div>
    <?php endif; ?>
</body>
</html>
