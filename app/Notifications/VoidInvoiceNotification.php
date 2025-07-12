<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VoidInvoiceNotification extends Notification
{
    use Queueable;

    public $invoice;
    public $voidedBy;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice, User $voidedBy, string $reason)
    {
        $this->invoice = $invoice;
        $this->voidedBy = $voidedBy;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $invoiceNumber = str_pad($this->invoice->invoice_number, 4, '0', STR_PAD_LEFT);

        return (new MailMessage)
            ->subject('🚨 ALERTA: Factura Anulada - Operación Delicada')
            ->greeting('¡Atención Administrador!')
            ->line("Se ha anulado la factura #{$invoiceNumber} del cliente {$this->invoice->client->name}.")
            ->line("**Usuario que anuló:** {$this->voidedBy->name} ({$this->voidedBy->email})")
            ->line("**Razón de anulación:** {$this->reason}")
            ->line("**Fecha de anulación:** {$this->invoice->voided_at->format('d/m/Y H:i:s')}")
            ->line("**Total de la factura:** {$this->invoice->currency} " . number_format($this->invoice->total, 2))
            ->action('Ver Factura', route('invoices.show', $this->invoice))
            ->line('Esta es una operación delicada que requiere su atención.')
            ->salutation('Sistema de Facturación');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'client_name' => $this->invoice->client->name,
            'voided_by' => $this->voidedBy->name,
            'voided_by_email' => $this->voidedBy->email,
            'reason' => $this->reason,
            'total' => $this->invoice->total,
            'currency' => $this->invoice->currency,
            'voided_at' => $this->invoice->voided_at,
        ];
    }
}
