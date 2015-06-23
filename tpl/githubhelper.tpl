<!-- BEGIN: MAIN -->

<div class="container container-fluid">
	<div class="page-header">
		<h1>{PAGEADD_PAGETITLE}</h1>
	</div>
	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

	<form action="{FORM_SEND}" enctype="multipart/form-data" method="post" name="pageform">


		<div class="">
			<div class="form-group">
				<label>{PHP.L.repo_href}</label>
				{FORM_GITHUB}
			</div>
			<div class="form-group">
				<label>{PHP.L.Category}</label>
				{FORM_CAT}
			</div>	
			{FORM_TEXT}

		</div>
		<div class="clearfix"></div>

		<div class="publish margintop10 marginbottom10">
			<button type="submit" name="rpagestate" value="0" class="btn btn-primary">{PHP.L.Publish}</button>
		</div>
	</form>

	<div class="alert alert-info">{PHP.L.page_formhint}</div>			
</div>


<!-- END: MAIN -->