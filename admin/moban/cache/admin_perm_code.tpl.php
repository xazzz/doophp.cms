<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./admin.htm">管理员管理</a> > <a href="./admin.htm?action=perm&amp;uid=<?=$uid?>">权限设置</a> > 管理组：<?=$udb['perm']?></div>
</div>
<div class="clear"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text">权限代码：</td>
		<td colspan="2" class="input">
			<textarea style="width:90%;height:200px;font-family:Courier New;"><?=$__code?></textarea>
		</td>
	</tr>
	<tr>
		<td class="text">&nbsp;</td>
		<td colspan="2">
			请把代码复制至 /<?=SUPER_MAN?>/config/acl.php 即可
		</td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
