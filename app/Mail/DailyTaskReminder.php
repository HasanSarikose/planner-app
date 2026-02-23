<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyTaskReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $tasks; // Görevleri view'a taşımak için public değişken

    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🗓️ Günaydın! Bugün Yapman Gerekenler',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_reminder', // Mailin tasarım dosyası
        );
    }
}
