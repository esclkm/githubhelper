<!-- BEGIN: MAIN -->
<!-- IF {ISCOT} -->
<div class="">
	<h4>Приложение Cotonti</h4>
	<div class="row">
		<div class="col-xs-2">
			<!-- IF {ICO} -->
			<img src="{ICO}">
			<!--ENDIF -->
		</div>
		<div class="col-xs-10 col-md-5">
			<dl class="dl-horizontal">
				<dt>Название</dt>
				<dd>{NAME} ({CODE})</dd>
				<dt>Версия</dt>
				<dd>{VERSION}</dd>
				<!-- IF {CATEGORY} -->
				<dt>Категория</dt>
				<dd>{CATEGORY_TITLE}</dd>
				<!-- ENDIF -->
			</dl>	
		</div>
		<div class="col-xs-10 col-xs-offset-2 col-md-5 col-md-offset-0">
			<dl class="dl-horizontal">
				<!-- IF {RECOMMENDS_MODULES} -->
				<dt>Требуются модули</dt>
				<dd>
					<!-- FOR {EXT} IN {RECOMMENDS_MODULES} -->
					<span class="label label-danger">{EXT}</span>
					<!-- ENDFOR -->
				</dd>
				<!-- ENDIF -->
				<!-- IF {RECOMMENDS_PLUGINS} -->
				<dt>Требуются плагины</dt>
				<dd>
					<!-- FOR {EXT} IN {RECOMMENDS_PLUGINS} -->
					<span class="label label-danger">{EXT}</span>
					<!-- ENDFOR -->

				</dd>
				<!-- ENDIF -->
			</dl>	
		</div>			
	</div>
<!-- ELSE -->	
<!-- ENDIF -->
</div>
<!-- END: MAIN -->