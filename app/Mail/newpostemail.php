<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class newpostemail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data=$data;
    }

    /* *
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     *//* 
    public function envelope()
    {
        return new Envelope(
            subject: 'New Post Email',
        );
    } */

    /**
     * Get the message content definition.
     *
     */
    /* public function content()
    {
        return new Content(
            view: 'view.name',
        );
    } */

    /**
     * Get the attachments for the message.
     *
     */
    //public function attachments()
    //{
    //    return [];
    //}

    /**
    * @return $this
    *
    */
    public function build(){
        return $this->subject('Congrats on the new post!!')->view('NewPostEmail',['title'=>$this->data['title'],'name'=>$this->data['name']]);
    }
}
