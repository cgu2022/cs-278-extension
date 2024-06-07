import { extend } from 'flarum/common/extend';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import Button from 'flarum/common/components/Button';
import app from 'flarum/forum/app';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';

app.initializers.add('cgu2022-cs-278-extension', () => {
  extend(DiscussionPage.prototype, 'view', function (vnode) {
    const discussion = this.discussion;
    const postSummaries = [];
    this.loading = this.loading || false;

    // Get the first five words of each post in the discussion, and then display them in a green box at the top of the discussion page.
    if (discussion && discussion.postIds()) {
        discussion.postIds().forEach(postId => {
            const post = app.store.getById('posts', postId);
            const authorName = post && post.user() ? post.user().username() : 'Unknown';
            const postContent = post ? post.contentPlain() : '';
            // const firstFiveWords = postContent.split(' ').slice(0, 5).join(' ');

            // Comment out the following line to stop displaying the posts in a green box
            // postSummaries.push(`${authorName}: ${postContent}`);
        });
    }

    vnode.children.unshift(
      m('div', {
        style: {
          backgroundColor: '#4CAF50', // Green background color
          color: 'white', // White text color
          textAlign: 'center',
          padding: '10px 0',
          fontSize: '20px',
          whiteSpace: 'pre-wrap',
          position: 'relative'
        },
      }, [
        // postSummaries.join('\n'), //this is for printing the summaries of the posts
        m('div', {
          style: {
            marginTop: '20px'
          }
        }, 
        m(Button, {
          className: 'Button Button--primary',
          onclick: () => {
            this.loading = true;
            m.redraw();
            app.request({
              method: 'POST',
              url: `${app.forum.attribute('apiUrl')}/generate-summary`,
              body: {
                data: {
                  discussionId: discussion.id(),
                }
              }
            }).then(response => {
              alert('Summary: ' + response.data.attributes.summary);
              console.log('OpenAI API full response:', response.data.attributes.response);
              this.loading = false;
              m.redraw();
            }).catch(error => {
              console.error('Error:', error);
              alert('An error occurred while generating the summary.');
              this.loading = false;
              m.redraw();
            });
          }
        }, 'Generate Summary with GPT')),
        this.loading && m(LoadingIndicator)
      ])
    );
  });
});
