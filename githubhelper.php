<?php

/* ====================
 * [BEGIN_COT_EXT]
 * Hooks=standalone
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
defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', 'a');
cot_block($usr['auth_write']);
// Additional API requirements
require_once cot_incfile('extrafields');

// Self requirements
require_once cot_incfile('page', 'module');
require_once cot_incfile('forms');

$m = cot_import('m', 'G', 'ALP');
$a = cot_import('a', 'G', 'ALP');
$field = 'page_github';

if($a == 'add')
{
	$page_cat = cot_import('rpagecat', 'P', 'TXT');	
	$page_github = cot_import('rpagegithub', 'P', 'TXT');	

	if (preg_match('/https:\/\/github.com\/([^\n\/]+)\/([^\n\/]+)/i', $page_github, $mt))
	{
		$github = new Github($cfg['plugin']['githubhelper']['client_id'], $cfg['plugin']['githubhelper']['client_secret']);
		$github->setCacheDir($cfg['cache_dir'].'/githubhelper');
		$github->setFilesDir($cfg['thumbs_dir'].'/githubhelper');
		$github_repos = array();
		try
		{
			$repo = $github->api('GET', "/repos/".$mt[1]."/".$mt[2]);
			if(!$repo)
			{
				cot_error('repo_githublinkincorrect', 'rpagegithub');
			}
			$_POST['rpagecat'] = $page_cat;
			$_POST['rpagegithub'] = $page_github;
			
			$_POST['rpagetitle'] = $repo['name'];
			$_POST['rpagedesc'] = $repo['description'];
			$_POST['rpagetext'] = (empty($_POST['rpagetext'])) ? $_POST['rpagedesc'] : $_POST['rpagetext'];
	
			$rpage = cot_page_import('POST', array(), $usr);
			cot_page_validate($rpage);

		}
		catch (Exception $e)
		{		
			cot_error($e->getMessage(), 'rpagegithub');
		}
	}
	else
	{
		cot_error('repo_nogithublink', 'rpagegithub');
	}
	if (!cot_error_found())
	{
		$id = cot_page_add($rpage, $usr);
		if(cot_plugin_active('autoalias2'))
		{
			require_once cot_incfile('autoalias2', 'plug');
			$rpage['page_alias'] = autoalias2_update($rpage['page_title'], $id);
		}
		cot_redirect(cot_url('page', "e=edit&a=update&id=".$id, '', true));
	}
	else
	{
		cot_redirect(cot_url('plug', "e=githubhelper", '', true));
	}
	cot_url('page', "e=edit&a=update&id=".$pag['page_id']);
}

$t = new XTemplate(cot_tplfile('githubhelper', 'plug'));
$t->assign(array(
	'FORM_SEND' => cot_url('plug', "e=githubhelper&a=add"),
	'FORM_CAT' => cot_selectbox_structure('page', $rpage['page_cat'], 'rpagecat'),
	'FORM_GITHUB' => cot_inputbox('text', 'rpagegithub', $rpage['page_github'], array('size' => '64', 'maxlength' => '255')),
	'FORM_TEXT' => cot_textarea('rpagetext', $rpage['page_text'], 24, 120, '', 'input_textarea_editor'),
));
cot_display_messages($t);