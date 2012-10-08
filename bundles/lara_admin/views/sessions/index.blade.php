<div id="content_wrapper">
	<div id="active_admin_content">
		<div id="login">
			<h2>Login</h2>
			{{ Form::open('lara_admin/sign_in', 'POST', array("class"=>"formtastic admin_user", "novalidate"=>"novalidate", "id"=>"session_new", "accept-charset"=>"UTF-8") ) }}
				<fieldset class="inputs">
					<ol>
						<li class="email input required stringish" id="admin_user_email_input">
							<label class=" label" for="admin_user_email">Email<abbr title="required">*</abbr></label>
							<input id="login" maxlength="255" name="login" type="email" value=""/>
						</li>
						<li class="password input required stringish" id="admin_user_password_input">
							<label class=" label" for="admin_user_password">Contraseña<abbr title="required">*</abbr>
							</label><input id="password" maxlength="128" name="password" type="password"/>
						</li>
						<li class="boolean input optional" id="admin_user_remember_me_input">
							<input name="admin_user[remember_me]" type="hidden" value="0"/>
						</li>
					</ol>
				</fieldset>
				<fieldset class="buttons">
					<ol>
						<li class="commit button"><input class="create" id="admin_user_submit" name="commit" type="submit" value="Entrar"/></li>
					</ol>
				</fieldset>
			{{ Form::close()}}
		</div>
	</div>
</div>