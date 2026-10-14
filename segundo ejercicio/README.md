# Emision de factura en PHP

Programa que emite la factura de la compra de unidades de un articulo.

## Funcionamiento

1. El usuario completa un formulario HTML con:
   - Descripcion del articulo
   - Cantidad adquirida (entero ≥ 1)
   - Precio unitario de venta (> 0)
2. Al enviarlo, `index.php` valida los datos y calcula:
   - **Subtotal (precio bruto)** = cantidad × precio unitario
   - **IVA (12%)** = subtotal × 0,12
   - **Total con IVA** = subtotal + IVA
   - **Descuento**: si el total con IVA supera **150 $**, se descuenta el **15%** sobre ese total
   - **Total neto a pagar** = total con IVA − descuento
3. Se muestra el desglose completo: subtotal, IVA, si aplica o no el descuento y el monto neto.

## Ejemplo

Cantidad 10 × 20 $ → subtotal 200,00 $ → IVA 24,00 $ → total con IVA 224,00 $ → descuento 33,60 $ → **neto 190,40 $**.

## Ejecucion

Requiere PHP 7.4 o superior. 

Luego abrir <http://localhost/emily/segundo%20ejercicio/index.php/> en el navegador.
