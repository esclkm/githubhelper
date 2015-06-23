<?php

/**
 * [BEGIN_COT_EXT]
 * Hooks=global
 * [END_COT_EXT]
 */
/**
 * plugin Get Github Info for Cotonti Siena
 * 
 * @package githubhelper
 * @version 1.0.0
 * @author esclkm
 * @copyright 
 * @license BSD
 *  */
// Generated by Cotonti developer tool (littledev.ru)
defined('COT_CODE') or die('Wrong URL.');

require_once($cfg['plugins_dir']."/githubhelper/inc/github.php");
require_once($cfg['plugins_dir']."/githubhelper/inc/Parsedown.php");

$github = new Github($cfg['plugin']['githubhelper']['client_id'], $cfg['plugin']['githubhelper']['client_secret']);
$github->setCacheDir($cfg['cache_dir'].'/githubhelper');
$github->setFilesDir($cfg['thumbs_dir'].'/githubhelper');
$github_repos = array();

//cot_print($github->rate());
// зарегистрировать приложение
//https://github.com/settings/applications/new
//там будут ключи
//$github = new GithubOAuth();
function get_repo($path)
{
	global $github, $github_repos;
	$mt = array();
	if(isset($github_repos[$path]))
	{
		return $github_repos[$path];		
	}
	if (preg_match('/https:\/\/github.com\/([^\n\/]+)\/([^\n\/]+)/i', $path, $mt))
	{
		try
		{
			$repo = $github->api('GET', "/repos/".$mt[1]."/".$mt[2]);
			if(!$repo)
			{
				return false;
			}
			$repo['created_at_stamp'] = strtotime($repo['created_at']);
			$repo['updated_at_stamp'] = strtotime($repo['updated_at']);
			$repo['pushed_at_stamp'] = strtotime($repo['pushed_at']);		
			$repo['nfo']['owner'] = $mt[1];
			$repo['nfo']['repo'] = $mt[2];
			$github_repos[$path] = $repo;
			
			return $repo;
		}
		catch (Exception $e)
		{
			return false;
		}
	}	
	return false;
}
function headerminus($text)
{
	$text = str_ireplace(array('<h4','</h4>'),array('<h6','</h6>'),$text);
	$text = str_ireplace(array('<h3','</h3>'),array('<h5','</h5>'),$text);
	$text = str_ireplace(array('<h2','</h2>'),array('<h4','</h4>'),$text);
	$text = str_ireplace(array('<h1','</h1>'),array('<h3','</h3>'),$text);
	return $text;
}

function github_repoinfo($path, $tpl = NULL, $count = 10 )
{
	global $github;
	if($repo = get_repo($path))
	{
		try
		{
			$Parsedown = new Parsedown();
			$t = new XTemplate(cot_tplfile('githubhelper.repoinfo.'.$tpl, 'plug'));
			$t->assign("REPOINFO", $repo);

			$t->assign("OWNER", $repo['nfo']['owner']);
			$t->assign("REPO", $repo['nfo']['repo']);		
			$t->parse('MAIN');
			return $t->text('MAIN');
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}
	return false;
}

function github_issues($path, $tpl = NULL, $count = 10 )
{
	global $github;
	if($repo = get_repo($path))
	{
		try
		{
			$Parsedown = new Parsedown();

			$issues = $github->api('GET', "/repos/".$repo['nfo']['owner']."/".$repo['nfo']['repo']."/issues", array('page' => 1, 'per_page' => $count));
			$t = new XTemplate(cot_tplfile('githubhelper.issues.'.$tpl, 'plug'));
	//		cot_print($github->api('GET', "/repos/".$repo['nfo']['owner']."/".$repo['nfo']['repo']."/issues", array('page' => 1, 'per_page' => $count), null, true, false, true));
			$issues = is_array($issues) ? $issues : array();
			foreach ($issues as $row)
			{
				//	cot_print($row);
				$row['parsedbody'] = headerminus($Parsedown->text($row['body']));
				$row['created_at_stamp'] = strtotime($row['created_at']);
				$row['updated_at_stamp'] = strtotime($row['updated_at']);
				$t->assign("ISSUE", $row);
				$t->parse('MAIN.ROW');
			}
			$t->assign("OWNER", $repo['nfo']['owner']);
			$t->assign("REPO", $repo['nfo']['repo']);
			$t->assign("REPOINFO", $repo);
			$t->parse('MAIN');
			return $t->text('MAIN');
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}
	return false;
}

function github_commits($path, $tpl = NULL, $count = 10 )
{
	global $github;
	if($repo = get_repo($path))
	{
		try
		{
			$Parsedown = new Parsedown();

			$commits = $github->api('GET', "/repos/".$repo['nfo']['owner']."/".$repo['nfo']['repo']."/commits", array('page' => 1, 'per_page' => $count));
			$t = new XTemplate(cot_tplfile('githubhelper.commits.'.$tpl, 'plug'));
		//	cot_print($github->api('GET', "/repos/".$repo['nfo']['owner']."/".$repo['nfo']['repo']."/commits", array('page' => 1, 'per_page' => $count), null, true, false, true),
		//		$github->rate());
			$commits = is_array($commits) ? $commits : array();
			foreach ($commits as $row)
			{
				//	cot_print($row);
				$row['commit']['parsedmessage'] = headerminus($Parsedown->text($row['commit']['message']));
				$row['commit']['author']['date_stamp'] = strtotime($row['commit']['author']['date']);
				$row['commit']['committer']['date_stamp'] = strtotime($row['commit']['committer']['date']);
				$t->assign("COMMIT", $row);
				$t->parse('MAIN.ROW');
			}
			$t->assign("OWNER", $repo['nfo']['owner']);
			$t->assign("REPO", $repo['nfo']['repo']);
			$t->assign("REPOINFO", $repo);
			$t->parse('MAIN');
			return $t->text('MAIN');
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}
	return false;		
}

function github_readme($path)
{
	//repos/:owner/:repo/commits
	global $github;
	if($repo = get_repo($path))
	{
		try
		{
			$Parsedown = new Parsedown();

			$readme = $github->api('GET', "/repos/".$repo['nfo']['owner']."/".$repo['nfo']['repo']."/readme");
			return headerminus($Parsedown->text(base64_decode($readme['content'])));

		}
		catch (Exception $e)
		{
			return null;
		}
	}
	return false;
}
function github_download($path)
{
	//repos/:owner/:repo/:archive_format/:ref
	global $github;
	if($repo = get_repo($path))
	{
		try
		{
			$link = $github->api('GET', "/repos/".$repo['nfo']['owner']."/".$repo['nfo']['repo']."/zipball", '', null, false);
			//cot_print($link);
			return $link;
		}
		catch (Exception $e)
		{
			return null;
		}
	}
	return false;		
}

function github_userrepos($user, $tpl = NULL, $count = 10 )
{
	//repos/:owner/:repo/commits
	global $github;
	try
	{

		$Parsedown = new Parsedown();

		$repos = $github->api('GET', "/users/$user/repos", array('page' => 1, 'per_page' => $count, 'sort' => 'updated'));
		$t = new XTemplate(cot_tplfile('githubhelper.repos.'.$tpl, 'plug'));

		foreach ($repos as $row)
		{
			$row['created_at_stamp'] = strtotime($row['created_at']);
			$row['updated_at_stamp'] = strtotime($row['updated_at']);
			$row['pushed_at_stamp'] = strtotime($row['pushed_at']);
			$t->assign("REPO", $row);
			$t->parse('MAIN.ROW');
		}
		$t->assign("USER", $user);		
		$t->parse('MAIN');
		
		return $t->text('MAIN');
	}
	catch (Exception $e)
	{
		return $e->getMessage();
	}
}

function github_setupfile($path)
{
	//repos/:owner/:repo/commits
	global $github;
	if($repo = get_repo($path, $tpl = ""))
	{
		
		try
		{
			$Parsedown = new Parsedown();

			$contents = $github->api('GET', "/repos/".$repo['nfo']['owner']."/".$repo['nfo']['repo']."/contents");
			$cot_ext = false;
			$cot_ext_code = '';
			$cot_ext_ico = '';
			foreach($contents as $file)
			{
				if (preg_match('/(.+)?\.setup\.php$/i', $file['name'], $mt))
				{
					$file_content = $github->api('GET', "/repos/".$repo['nfo']['owner']."/".$repo['nfo']['repo']."/contents/".$file['path']);
					$text = base64_decode($file_content['content']);
					$params = github_cot_infoget($text);
					$cot_ext = true;
					$cot_ext_code = $mt[1];
					break;
				}
			}
			if($cot_ext)
			{
				foreach($contents as $file)
				{
					if ($file['name'] == $cot_ext_code.".png")
					{
						$file_content = $github->api('GET', "/repos/".$repo['nfo']['owner']."/".$repo['nfo']['repo']."/contents/".$file['path']);
						$cot_ext_ico = $file_content['download_url'];
						break;
					}
				}				
			}
			require_once cot_langfile('admin', 'core');
			$t = new XTemplate(cot_tplfile('githubhelper.setupfile.'.$tpl, 'plug'));

			$t->assign(array(
				'ISCOT' => $cot_ext,
				'CODE' => $cot_ext_code,
				'ICO' => $cot_ext_ico,
				'NAME' => $params['Name'],
				'DESC' => $params['Description'],
				'VERSION' => $params['Version'],
				'DATE' => $params['Date'],
				'AUTHOR' => $params['Author'],
				'COPYRIGHT' => $params['Copyright'],
				'NOTES' => $params['Notes'],
				'CATEGORY' => $params['Category'],
				'CATEGORY_TITLE' => $L['ext_cat_' . $params['Category']],
				'RECOMMENDS_MODULES' => array_filter(array_map('trim', explode(',', $params['Recommends_modules']))),
				'RECOMMENDS_PLUGINS' => array_filter(array_map('trim', explode(',', $params['Recommends_plugins']))),
				'REQUIRES_MODULES' => array_filter(array_map('trim', explode(',', $params['Requires_modules']))),
				'REQUIRES_PLUGINS' => array_filter(array_map('trim', explode(',', $params['Requires_plugins']))),				
			));
			$t->parse('MAIN');
			return $t->text('MAIN');
		}
		catch (Exception $e)
		{
			return null;
		}
	}
	return false;
}

function github_cot_infoget($text, $limiter = 'COT_EXT', $maxsize = 32768)
{
	$result = array();

	if ($text)
	{
		$limiter_begin = '[BEGIN_' . $limiter . ']';
		$limiter_end = '[END_' . $limiter . ']';
		$data = $text;
		$begin = mb_strpos($data, $limiter_begin);
		$end = mb_strpos($data, $limiter_end);

		if ($end > $begin && $begin > 0)
		{
			$lines = mb_substr($data, $begin + 8 + mb_strlen($limiter),
				$end - $begin - mb_strlen($limiter) - 8);
			$lines = explode("\n", $lines);

			foreach ($lines as $line)
			{
				$line = ltrim($line, " */");
				$linex = explode('=', $line);
				$ii = 1;
				while (!empty($linex[$ii]))
				{
					$result[$linex[0]] .= trim($linex[$ii]);
					$ii++;
				}
			}
		}
		else
		{
			$result = false;
		}
	}
	else
	{
		$result = false;
	}
	return $result;
}


function github_screenshots($path,$tpl = '', $count = 0, $asarray = false)
{
	//repos/:owner/:repo/commits
	global $github;
	if($repo = get_repo($path, $tpl = ""))
	{
		
		try
		{
			$Parsedown = new Parsedown();

			if($contents = $github->api('GET', "/repos/".$repo['nfo']['owner']."/".$repo['nfo']['repo']."/contents/scr"))
			{
				if(!count($contents))
				{
					return false;
				}
				if(!$asarray)
				{
					$t = new XTemplate(cot_tplfile('githubhelper.screenshots.'.$tpl, 'plug'));
					$i = 0;
					foreach($contents as $file)
					{
						$i++;
						if($count != 0 && $i > $count)
						{
							break;
						}
						$t->assign(array(
							"NAME" => $file['name'],
							"PATH" => $github->download($file['download_url']),
							"NUM" => $i
						));
						$t->parse('MAIN.ROW');
					}
					$t->parse('MAIN');
					return $t->text('MAIN');
				}
				else
				{
					$ret_array=array();
					foreach($contents as $file)
					{
						$i++;
						if($count != 0 && $i > $count)
						{
							break;
						}
						$ret_array[$i] = array(
							"NAME" => $file['name'],
							"PATH" => $github->download($file['download_url']),
						);
						
						if($count == 1)
						{
							return $ret_array[$i]['PATH'];
						}

					}
					return $ret_array;
				}
			}
			return false;
		}
		catch (Exception $e)
		{
			return null;
		}
	}
	return false;
}
