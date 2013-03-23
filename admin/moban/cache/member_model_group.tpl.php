<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./member.htm?action=model">会员模型</a> > <a href="./member.htm?action=user&amp;id=<?=$id?>"><?=$db['modelname']?></a> > 用户组管理</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td class="centle" colspan="2" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
			<a href="./member.htm?action=group_add&amp;model_id=<?=$id?>">+新增用户组</a>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table cellpadding="0" cellspacing="0" class="table neitable" style="margin:0px; border:none;">
				<tr id="list-top">
					<td class="list" width="40" style="padding:0px; text-align:center;">ID</td>
					<td width="60" class="list" style="padding:0px; text-align:center;">排序</td>
					<td class="list">用户组</td>
					<td class="list">所需积分</td>
					<td width="10%" class="list" >操作</td>
				</tr><? if(is_array($list)) { foreach($list as $v) { ?>				<tr class="mouse click">
					<td class="list-text"><?=$v['id']?></td>
					<td class="list-text"><input type="text" value="<?=$v['vieworder']?>" name="order_new[<?php echo isset($v['id']) ? $v['id'] : "";?>]" class="text no_order" /></td>
					<td class="list-text"><?=$v['name']?></td>
					<td class="list-text"><?=$v['credit']?></td>
					<td class="list-text"><a href="./member.htm?action=group_edit&amp;model_id=<?=$id?>&amp;id=<?=$v['id']?>">修改</a> <a href="./member.htm?action=group_del&amp;model_id=<?=$id?>&amp;id=<?=$v['id']?>" onclick="return confirm('确认要删除此用户组吗?');">删除</a></td>
				</tr>
				<? } } ?></table>
		</td>
	</tr>
	<tr> 
		<td class="all-submit" style="padding:5px 0px;" colspan="2">
			<input name="submit" type='submit' value='保存' class="submit li-submit" onclick="post('./member.htm?action=group_order&id=<?=$id?>');"/>
		</td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
