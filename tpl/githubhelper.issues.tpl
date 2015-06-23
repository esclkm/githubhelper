<!-- BEGIN: MAIN -->
<div class="">
	<h4>Задачи</h4>
<!-- BEGIN: ROW -->
	<div class="rsselem">
		<div class="row">
			<div class="col-sm-2"><img class="img-responsive img-thumbnail" src="{ISSUE.user.avatar_url}" /></div>
			<div class="col-sm-10">
				<div>
					<a rel="nofollow noindex" target="_blank" href="{ISSUE.user.html_url}">{ISSUE.user.login}</a>
					<span class="small pull-right">{ISSUE.updated_at_stamp|cot_date('date_text', $this)}</span>
				</div>
				<h5>
					<a rel="nofollow noindex" target="_blank" href="{ISSUE.html_url}" title="{ISSUE.title}">{ISSUE.title}</a>
				</h5>
				<div>{ISSUE.parsedbody|cot_string_truncate($this, 500, 1, 0, '...')}</div>
				<div class="small text-right"><a rel="nofollow noindex" target="_blank" href="{ISSUE.html_url}">{PHP.L.More}</a></div>
			</div>
		</div>
	</div>
<!-- END: ROW -->
	<div class="text-right"><a rel="nofollow noindex" target="_blank" href="https://github.com/{OWNER}/{REPO}/issues">Все задачи ({REPOINFO.open_issues_count})</a></div>
</div>
<!-- END: MAIN -->

array (size=19)
  'url' => string 'https://api.github.com/repos/esclkm/rssreader/issues/2' (length=54)
  'labels_url' => string 'https://api.github.com/repos/esclkm/rssreader/issues/2/labels{/name}' (length=68)
  'comments_url' => string 'https://api.github.com/repos/esclkm/rssreader/issues/2/comments' (length=63)
  'events_url' => string 'https://api.github.com/repos/esclkm/rssreader/issues/2/events' (length=61)
  'html_url' => string 'https://github.com/esclkm/rssreader/issues/2' (length=44)
  'id' => int 75887669
  'number' => int 2
  'title' => string 'Проверить работу кэша' (length=40)
  'user' => 
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
  'labels' => 
    array (size=1)
      0 => 
        array (size=3)
          'url' => string 'https://api.github.com/repos/esclkm/rssreader/labels/wontfix' (length=60)
          'name' => string 'wontfix' (length=7)
          'color' => string 'ffffff' (length=6)
  'state' => string 'open' (length=4)
  'locked' => boolean false
  'assignee' => 
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
  'milestone' => null
  'comments' => int 1
  'created_at' => string '2015-05-13T07:47:13Z' (length=20)
  'updated_at' => string '2015-05-13T09:21:24Z' (length=20)
  'closed_at' => null
  'body' => string '*Очень важно*
Очень важно, чтобы кэш работал

очень очень

# Проверка #
    
    codetext
    
отлично' (length=171)