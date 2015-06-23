<!-- BEGIN: MAIN -->
<div class="">
	<h4>Коммиты</h4>
<!-- BEGIN: ROW -->
	<div class="rsselem">
		<div class="row">
			<div class="col-sm-2"><img class="img-responsive img-thumbnail" src="{COMMIT.author.avatar_url}" /></div>
			<div class="col-sm-10">
				<div>
					<a rel="nofollow noindex" target="_blank" href="{COMMIT.author.html_url}">{COMMIT.author.login}</a>
					<span class="small pull-right">{COMMIT.commit.author.date_stamp|cot_date('date_text', $this)}</span>
				</div>
				<div>{COMMIT.commit.parsedmessage|cot_string_truncate($this, 500, 1, 0, '...')}</div>
				<div class="small text-right"><a rel="nofollow noindex" target="_blank" href="{COMMIT.html_url}">{PHP.L.More}</a></div>
			</div>
		</div>
	</div>
<!-- END: ROW -->
	<div class="text-right"><a rel="nofollow noindex" target="_blank" href="https://github.com/{OWNER}/{REPO}/commits">Все коммиты</a></div>
</div>
<!-- END: MAIN -->
 
array (size=8)
  'sha' => string 'd1ca95ead3fe5c672549f8e61b910e6f6d07a956' (length=40)
  'commit' => 
    array (size=6)
      'author' => 
        array (size=3)
          'name' => string 'esclkm' (length=6)
          'email' => string 'esclkm@gmail.com' (length=16)
          'date' => string '2015-05-13T07:41:08Z' (length=20)
      'committer' => 
        array (size=3)
          'name' => string 'esclkm' (length=6)
          'email' => string 'esclkm@gmail.com' (length=16)
          'date' => string '2015-05-13T07:41:08Z' (length=20)
      'message' => string 'Теперь используется curl , а не fopen
и вывод штампов дат' (length=94)
      'tree' => 
        array (size=2)
          'sha' => string '3d9ae4920586dde5c2e9c80a15168126320bd3aa' (length=40)
          'url' => string 'https://api.github.com/repos/esclkm/rssreader/git/trees/3d9ae4920586dde5c2e9c80a15168126320bd3aa' (length=96)
      'url' => string 'https://api.github.com/repos/esclkm/rssreader/git/commits/d1ca95ead3fe5c672549f8e61b910e6f6d07a956' (length=98)
      'comment_count' => int 0
  'url' => string 'https://api.github.com/repos/esclkm/rssreader/commits/d1ca95ead3fe5c672549f8e61b910e6f6d07a956' (length=94)
  'html_url' => string 'https://github.com/esclkm/rssreader/commit/d1ca95ead3fe5c672549f8e61b910e6f6d07a956' (length=83)
  'comments_url' => string 'https://api.github.com/repos/esclkm/rssreader/commits/d1ca95ead3fe5c672549f8e61b910e6f6d07a956/comments' (length=103)
  'author' => 
    array (size=17)
      'login' => string 'esclkm' (length=6)
      'id' => int 780145
      'avatar_url' => string 'https://avatars.githubusercontent.com/u/780145?v=3' (length=50)
      'gravatar_id' => string '' (length=0)
      'url' => string 'https://api.github.com/users/esclkm' (length=35)
      'html_url' => string 'https://github.com/esclkm' (length=25)
      'followers_url' => string 'https://api.github.com/users/esclkm/followers' (length=45)
      'following_url' => string 'https://api.github.com/users/esclkm/following{/other_user}' (length=58)
      'gists_url' => string 'https://api.github.com/users/esclkm/gists{/gist_id}' (length=51)
      'starred_url' => string 'https://api.github.com/users/esclkm/starred{/owner}{/repo}' (length=58)
      'subscriptions_url' => string 'https://api.github.com/users/esclkm/subscriptions' (length=49)
      'organizations_url' => string 'https://api.github.com/users/esclkm/orgs' (length=40)
      'repos_url' => string 'https://api.github.com/users/esclkm/repos' (length=41)
      'events_url' => string 'https://api.github.com/users/esclkm/events{/privacy}' (length=52)
      'received_events_url' => string 'https://api.github.com/users/esclkm/received_events' (length=51)
      'type' => string 'User' (length=4)
      'site_admin' => boolean false
  'committer' => 
    array (size=17)
      'login' => string 'esclkm' (length=6)
      'id' => int 780145
      'avatar_url' => string 'https://avatars.githubusercontent.com/u/780145?v=3' (length=50)
      'gravatar_id' => string '' (length=0)
      'url' => string 'https://api.github.com/users/esclkm' (length=35)
      'html_url' => string 'https://github.com/esclkm' (length=25)
      'followers_url' => string 'https://api.github.com/users/esclkm/followers' (length=45)
      'following_url' => string 'https://api.github.com/users/esclkm/following{/other_user}' (length=58)
      'gists_url' => string 'https://api.github.com/users/esclkm/gists{/gist_id}' (length=51)
      'starred_url' => string 'https://api.github.com/users/esclkm/starred{/owner}{/repo}' (length=58)
      'subscriptions_url' => string 'https://api.github.com/users/esclkm/subscriptions' (length=49)
      'organizations_url' => string 'https://api.github.com/users/esclkm/orgs' (length=40)
      'repos_url' => string 'https://api.github.com/users/esclkm/repos' (length=41)
      'events_url' => string 'https://api.github.com/users/esclkm/events{/privacy}' (length=52)
      'received_events_url' => string 'https://api.github.com/users/esclkm/received_events' (length=51)
      'type' => string 'User' (length=4)
      'site_admin' => boolean false
  'parents' => 
    array (size=1)
      0 => 
        array (size=3)
          'sha' => string 'df6185d918f9e5433e4a149ee0d6c56f698aba36' (length=40)
          'url' => string 'https://api.github.com/repos/esclkm/rssreader/commits/df6185d918f9e5433e4a149ee0d6c56f698aba36' (length=94)
          'html_url' => string 'https://github.com/esclkm/rssreader/commit/df6185d918f9e5433e4a149ee0d6c56f698aba36' (length=83)