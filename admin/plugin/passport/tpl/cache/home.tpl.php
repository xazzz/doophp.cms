<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./plugin.htm">插件管理</a> > <a href="./plugin.htm?action=passport">通行证</a></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td class="centle" height="20" colspan="20" style="font-weight:normal;">
			<div style="float:left;">以下为已开通的通行证的接口，登录和注册在原论坛，这里采用MySql来实现会员同步登录。</div>
		</td>
	</tr>
	<tr>
		<td class="list" colspan='20'>PHPWind</td>
	</tr>
	<tr class="mouse click">
		<td class="list-text">5.0.1</td>
		<td class="list-text">5.3</td>
		<td class="list-text">6.0</td>
		<td class="list-text">6.3</td>
		<td class="list-text">6.3.2</td>
		<td class="list-text">7.0</td>
		<td class="list-text">7.3</td>
		<td class="list-text">7.3.2</td>
		<td class="list-text">7.5</td>
		<td class="list-text">8.0</td>
		<td class="list-text">8.3</td>
		<td class="list-text">8.5</td>
		<td class="list-text">8.7</td>
		<td class="list-text">9.0</td>
	</tr>
	<tr>
		<td class="list" colspan='20'>Discuz</td>
	</tr>
	<tr class="mouse click">
		<td class="list-text">5.0</td>
		<td class="list-text">5.5</td>
		<td class="list-text">6.0</td>
		<td class="list-text">6.0.1</td>
		<td class="list-text">6.1</td>
		<td class="list-text">7.0</td>
		<td class="list-text">7.1</td>
		<td class="list-text">7.2</td>
		<td class="list-text">X1.0</td>
		<td class="list-text">X1.5</td>
		<td class="list-text">X2.0</td>
		<td class="list-text">X2.5</td>
		<td class="list-text">&nbsp;</td>
		<td class="list-text">&nbsp;</td>
	</tr>
	<tr>
		<td colspan='20' height='20' bgcolor="white">&nbsp;</td>
	<tr>
		<td class="centle" height="20" colspan="20" style="font-weight:normal;">
			<div style="float:left;">
				安装使用说明：<br>
				1、找到对应的版本api，它存放于admin/plugin/passport/api/下。<br>
				2、找开php文件，修改对应的参数。<br>
				3、配置数据库。<br>
				4、修改子系统下的auth.php里的beforeRun()。<br>
				5、完成。<br><br>
				Ps：<br>
				1、固定的参数有uid, username(用户名), icon(头像), sex(性别: 0为保密, 1为男, 2为女)。<br>
				2、头像的读取请查看readme.php里的说明。
			</div>
		</td>
	</tr>
</table>
<? include Lua::display('_foot',$this->dir); ?>
