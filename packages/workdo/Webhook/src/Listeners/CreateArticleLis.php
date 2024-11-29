<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Internalknowledge\Entities\Book;
use Workdo\Internalknowledge\Events\CreateArticle;
use Workdo\Webhook\Entities\SendWebhook;

class CreateArticleLis
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CreateArticle $event)
    {
        if (module_is_active('Webhook')) {
            $article = $event->article;

            $book = Book::find($article->book);

            $web_array = [
                'Book Title' => $book->title,
                'Article Title' => $article->title,
                'Article Description' => $article->description,
                'Article Type' => $article->type,
                'Article Content' => strip_tags($article->content)
            ];

            $action = 'New Artical';
            $module = 'Internalknowledge';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
