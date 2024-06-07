<?php

namespace CGU2022\CS278Extension\Api;

use CGU2022\CS278Extension\Api\Serializer\SummarySerializer;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\Discussion\DiscussionRepository;
use Flarum\Post\PostRepository;
use Flarum\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use OpenAI;
use Illuminate\Support\Arr;

class GenerateSummaryController extends AbstractShowController
{
    public $serializer = SummarySerializer::class;
    public $include = [];
    public $optionalInclude = [];
    public $maxLimit = 1;
    public $limit = 1;

    protected $discussions;
    protected $posts;

    public function __construct(DiscussionRepository $discussions, PostRepository $posts)
    {
        $this->discussions = $discussions;
        $this->posts = $posts;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $params = Arr::get($request->getParsedBody(), 'data', []);

        $discussionId = Arr::get($params, 'discussionId');
        $discussion = $this->discussions->findOrFail($discussionId, $actor);
        $posts = $discussion->posts()->orderBy('created_at')->get();

        $openaiApiKey = app('flarum.settings')->get('cgu2022.cs-278-extension.api_key');
        $client = OpenAI::client($openaiApiKey);

        $messages = $this->prepareMessages($discussion, $posts);

        try {
            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
            ]);

            $summary = $result->choices[0]->message->content;
            // Print out the full response for debugging
            error_log('OpenAI API response: ' . print_r($result, true));

            return (object) [
                'id' => $discussionId,
                'summary' => $summary,
                'response' => $result,  // Include full response for debugging
            ];
        } catch (\Exception $e) {
            // Log the error message for debugging
            error_log('OpenAI API error: ' . $e->getMessage());
            return (object) [
                'id' => $discussionId,
                'summary' => 'Failed to generate summary.',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function prepareMessages($discussion, $posts)
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant. Please summarize the following discussion.'],
            ['role' => 'user', 'content' => 'Discussion title: ' . $discussion->title],
        ];

        foreach ($posts as $post) {
            $author = $post->user->username;
            $content = substr($post->content, 0, 100); // Adjust this limit as needed
            $messages[] = ['role' => 'user', 'content' => "{$author}: {$content}"];
        }

        return $messages;
    }
}
