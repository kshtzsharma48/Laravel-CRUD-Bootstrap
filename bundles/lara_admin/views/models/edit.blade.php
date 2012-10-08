<div class="modal-body">
	<div id="main_content">
		<?php
		$arrayMultiPart = "";
		if (isset($model->multipartFormData) && $model->multipartFormData == true) {
			$arrayMultiPart = "multipart/form-data";
		}
		?>
		{{ Form::open("lara_admin/models/$modelName/$model->id", 'POST', array("class"=>"formtastic customer" , "enctype"=>$arrayMultiPart , "novalidate"=>"novalidate" ,"accept-charset"=>"UTF-8")) }}
			<div style="margin:0;padding:0;display:inline"><input name="utf8" type="hidden" value="&#x2713;"/>
				<input name="authenticity_token" type="hidden" value="R4quagirY/6leP6UBwANOrbzGw0hWelIYlnt8vd8UXY="/>
			</div>
			<fieldset class="inputs">
				<ol>
					@foreach( $model->edit as $id => $options )
						<li class="string input required stringish" id="customer_username_input">
							<?php $input = InputFactory::build($id, $options, $modelName, $model); ?>
							{{$input["label"]}}
							{{$input["input"]}}
						</li>
					@endforeach
				</ol>
			</fieldset>
			<fieldset class="buttons">
				<ol>
					<li class="commit button">
						<input class="create" id="customer_submit" name="commit" type="submit" value="Edit"></input>
					</li>
					<li class="cancel">
						{{ HTML::link("lara_admin/models/$modelName", "Cancel" ) }}
					</li>
				</ol>
			</fieldset>
		{{Form::close()}}
	</div>
</div>