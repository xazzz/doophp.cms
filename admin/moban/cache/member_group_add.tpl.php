<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./member.htm?action=model">会员模型</a> > <a href="./member.htm?action=user&amp;id=<?=$model_id?>"><?=$model_db['modelname']?></a> > <a href="./member.htm?action=model_group&amp;id=<?=$model_id?>">用户组管理</a> > <? if($db['id']) { ?>修改<? } else { ?>新增<? } ?>用户组</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>用户组名称：</td>
		<td colspan="2" class="input"><input name="name" type="text" class="text" maxlength='10' value="<?=$db['name']?>"></td>
	</tr>
	<tr> 
		<td class="text">基数积分：</td>
		<td colspan="2" class="input"><input name="credit" type="text" class="text med" maxlength='10' value="<?=$db['credit']?>"> (数字)</td>
	</tr>
	<tr> 
		<td class="text">有效期：</td>
		<td colspan="2" class="input"><input name="expiry" type="text" class="text med" maxlength='10' value="<?=$db['expiry']?>"> 天 (数字)</td>
	</tr>
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./member.htm?action=<?=$action?>');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
