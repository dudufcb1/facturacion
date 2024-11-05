<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Proforma {{ $invoice->invoice_number }}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      line-height: 1.4;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
    }

    .header h1 {
      margin: 0;
      font-size: 18px;
    }

    .company-info {
      margin-bottom: 10px;
      font-size: 11px;
    }

    .invoice-details {
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      position: relative;
      z-index: 0;
      /* Asegura que la tabla quede debajo */
      background: transparent;
    }


    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
    }

    th {
      background-color: #f8f8f8;
    }

    .totals {
      text-align: right;
    }

    .signatures {
      margin-top: 50px;
      display: flex;
      justify-content: space-between;
    }

    .signature-line {
      border-top: 1px solid #000;
      width: 200px;
      text-align: center;
      padding-top: 5px;
    }

    .logo {
      max-height: 100px;
      margin-bottom: 10px;
    }

    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-45deg);
      opacity: 0.2;
      z-index: 1;
      /* Cambiado de -1 a 1 */
      pointer-events: none;
      /* Permite hacer clic a través de la marca de agua */
    }

    .watermark img {
      max-width: 400px;
    }

    .container {
      position: relative;
      z-index: 0;
    }



    .status {
      margin-bottom: 10px;
      padding: 5px;
      text-align: center;
      font-weight: bold;
    }

    .status-paid {
      background-color: #d4edda;
      color: #155724;
    }

    .status-pending {
      background-color: #fff3cd;
      color: #856404;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Watermark based on status -->
    <div class="watermark">
      @if ($invoice->status === 'paid')
        <img src="{{ storage_path('app/public/company-logos/Pagado.png') }}" alt="Pagado">
      @else
        <img src="{{ storage_path('app/public/company-logos/Pendiente.png') }}" alt="Pendiente">
      @endif
    </div>

    <div class="header">
      <!-- Company Logo -->
      @if ($invoice->company->logo_path)
        <img src="{{ storage_path('app/public/' . $invoice->company->logo_path) }}" alt="Logo" class="logo">
      @endif

      <h1>Taller de Torno, Máquinas y Herramientas "{{ $invoice->company->name }}"</h1>
      <div class="company-info">
        {{ $invoice->company->owner_name }}<br>
        Tel: {{ $invoice->company->phones }}<br>
        {{ $invoice->company->address }}<br>
        RUC: {{ $invoice->company->ruc }}
      </div>
    </div>

    <!-- Status indicator -->
    <div class="status {{ $invoice->status === 'paid' ? 'status-paid' : 'status-pending' }}">
      Estado: {{ $invoice->status === 'paid' ? 'PAGADO' : 'PENDIENTE' }}
    </div>

    <div class="invoice-details">
      <table>
        <tr>
          <td><strong>PROFORMA N°:</strong> {{ str_pad($invoice->invoice_number, 4, '0', STR_PAD_LEFT) }}</td>
          <td>
            <strong>Fecha:</strong>
            {{ $invoice->issued_at->format('d/m/Y') }}
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <strong>Cliente:</strong> {{ $invoice->client->name }}<br>
            <strong>Dirección:</strong> {{ $invoice->client->address }}<br>
            <strong>RUC:</strong> {{ $invoice->client->document_number }}
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <strong>Estado:</strong> {{ $invoice->status === 'paid' ? 'PAGADO' : 'PENDIENTE' }}
          </td>
        </tr>
      </table>
    </div>

    <table>
      <thead>
        <tr>
          <th>Cant.</th>
          <th>DESCRIPCIÓN</th>
          <th>P.Unit.</th>
          <th>Precio Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($invoice->products as $product)
          <tr>
            <td>{{ $product->pivot->quantity }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ number_format($product->pivot->price, 2) }}</td>
            <td>{{ number_format($product->pivot->total, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="totals">
      <p><strong>TOTAL C$:</strong> {{ number_format($invoice->total, 2) }}</p>
    </div>

    <p>Elaborar Ck. a nombre de {{ $invoice->company->owner_name }}</p>

    <div class="signatures">
      <div class="signature-line">Elaborado Por</div>
      <div class="signature-line">Cliente</div>
    </div>
  </div>
</body>

</html>
