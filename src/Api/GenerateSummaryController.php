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
    // Specify the serializer class to be used for this controller.
    public $serializer = SummarySerializer::class;

    // Define the relationships to be included by default and optionally.
    public $include = [];
    public $optionalInclude = [];
    
    // Define pagination limits.
    public $maxLimit = 1;
    public $limit = 1;

    // Repositories for accessing discussions and posts.
    protected $discussions;
    protected $posts;

    // Constructor to inject the dependencies.
    public function __construct(DiscussionRepository $discussions, PostRepository $posts)
    {
        $this->discussions = $discussions;
        $this->posts = $posts;
    }

    // Method to handle the data processing for the API request.
    protected function data(ServerRequestInterface $request, Document $document)
    {
        // Get the actor (user) making the request.
        $actor = RequestUtil::getActor($request);

        // Extract parameters from the request body.
        $params = Arr::get($request->getParsedBody(), 'data', []);

        // Get the discussion ID from the parameters and fetch the discussion.
        $discussionId = Arr::get($params, 'discussionId');
        $discussion = $this->discussions->findOrFail($discussionId, $actor);

        // Get all posts in the discussion, ordered by creation date.
        $posts = $discussion->posts()->orderBy('created_at')->get();

        // Fetch the OpenAI API key from the settings.
        $openaiApiKey = app('flarum.settings')->get('cgu2022.cs-278-extension.api_key');
        
        // Initialize the OpenAI client.
        $client = OpenAI::client($openaiApiKey);

        // Prepare messages to be sent to the OpenAI API.
        $messages = $this->prepareMessages($discussion, $posts);

        try {
            // Make a request to the OpenAI API to generate a summary.
            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
            ]);

            // Extract the summary from the API response.
            $summary = $result->choices[0]->message->content;

            // Log the full response for debugging purposes.
            error_log('OpenAI API response: ' . print_r($result, true));

            // Return the summary and the full response.
            return (object) [
                'id' => $discussionId,
                'summary' => $summary,
                'response' => $result,
            ];
        } catch (\Exception $e) {
            // Log any errors encountered during the API request.
            error_log('OpenAI API error: ' . $e->getMessage());

            // Return a failure message in case of an error.
            return (object) [
                'id' => $discussionId,
                'summary' => 'Failed to generate summary.',
                'error' => $e->getMessage(),
            ];
        }
    }

    // Helper method to prepare messages for the OpenAI API request.
    private function prepareMessages($discussion, $posts)
    {
        // Initial system message and discussion title.
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant. Please summarize the following discussion.'],
            ['role' => 'user', 'content' => 'Discussion title: ' . $discussion->title],
        ];

        // Add each post's author and content to the messages.
        foreach ($posts as $post) {
            $author = $post->user->username;
            $content = substr($post->content, 0, 100); // Truncate content to the first 100 characters.
            $messages[] = ['role' => 'user', 'content' => "{$author}: {$content}"];
        }

        return $messages;
    }
}
