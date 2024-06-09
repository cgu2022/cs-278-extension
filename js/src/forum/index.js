import { extend } from 'flarum/common/extend'; // Importing the extend function from Flarum to extend components.
import DiscussionPage from 'flarum/forum/components/DiscussionPage'; // Importing the DiscussionPage component to extend its view.
import Button from 'flarum/common/components/Button'; // Importing the Button component to create a custom button.
import app from 'flarum/forum/app'; // Importing the app object to interact with the Flarum application.
import LoadingIndicator from 'flarum/common/components/LoadingIndicator'; // Importing the LoadingIndicator component to show a loading spinner.

app.initializers.add('cgu2022-cs-278-extension', () => { // Initializing the custom extension with a unique key.
  extend(DiscussionPage.prototype, 'view', function (vnode) { // Extending the view method of DiscussionPage.
    const discussion = this.discussion; // Getting the current discussion object.
    const postSummaries = []; // Array to store summaries of posts.
    this.loading = this.loading || false; // Initializing the loading state.

    // Get the first five words of each post in the discussion, and then display them in a green box at the top of the discussion page.
    if (discussion && discussion.postIds()) { // Checking if the discussion object and its post IDs exist.
        discussion.postIds().forEach(postId => { // Iterating over each post ID.
            const post = app.store.getById('posts', postId); // Getting the post object by ID.
            const authorName = post && post.user() ? post.user().username() : 'Unknown'; // Getting the author's username or 'Unknown' if not available.
            const postContent = post ? post.contentPlain() : ''; // Getting the plain content of the post.
            // const firstFiveWords = postContent.split(' ').slice(0, 5).join(' '); // Extracting the first five words of the post content (commented out).

            // Comment out the following line to stop displaying the posts in a green box
            // postSummaries.push(`${authorName}: ${postContent}`); // Pushing the summary into the array (commented out).
        });
    }

    vnode.children.unshift( // Adding a new element at the beginning of the vnode children array.
      m('div', {
        style: {
          backgroundColor: '#4CAF50', // Green background color.
          color: 'white', // White text color.
          textAlign: 'center', // Centered text alignment.
          padding: '10px 0', // Padding for the div.
          fontSize: '20px', // Font size.
          whiteSpace: 'pre-wrap', // Preserving whitespace formatting.
          position: 'relative' // Relative positioning.
        },
      }, [
        // postSummaries.join('\n'), // Joining the post summaries with newline characters (commented out).
        m('div', {
          style: {
            marginTop: '20px' // Margin at the top of the button.
          }
        }, 
        m(Button, {
          className: 'Button Button--primary', // Primary button styling.
          onclick: () => { // Handling the button click event.
            this.loading = true; // Setting the loading state to true.
            m.redraw(); // Redrawing the Mithril component.
            app.request({
              method: 'POST', // HTTP method.
              url: `${app.forum.attribute('apiUrl')}/generate-summary`, // API endpoint for generating the summary.
              body: {
                data: {
                  discussionId: discussion.id(), // Sending the discussion ID in the request body.
                }
              }
            }).then(response => { // Handling the successful response.
              alert('Summary: ' + response.data.attributes.summary); // Displaying the summary in an alert.
              console.log('OpenAI API full response:', response.data.attributes.response); // Logging the full API response.
              this.loading = false; // Setting the loading state to false.
              m.redraw(); // Redrawing the Mithril component.
            }).catch(error => { // Handling errors.
              console.error('Error:', error); // Logging the error.
              alert('An error occurred while generating the summary.'); // Displaying an error message.
              this.loading = false; // Setting the loading state to false.
              m.redraw(); // Redrawing the Mithril component.
            });
          }
        }, 'Generate Summary with GPT')), // Button label.
        this.loading && m(LoadingIndicator) // Displaying the loading indicator if the loading state is true.
      ])
    );
  });
});
