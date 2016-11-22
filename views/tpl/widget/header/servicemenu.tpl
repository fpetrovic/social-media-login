[{include file="widget/header/servicebox.tpl"}]

<ul id="topMenu">

    [{if !$oxcmp_user->oxuser__oxpassword->value}]

    <li><a id="registerLink" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=spxLogin" }]" title="[{oxmultilang ident="LOGIN"}]">[{oxmultilang ident="LOGIN"}]</a></li>
    <li class="login flyout[{if $oxcmp_user->oxuser__oxpassword->value}] logged[{/if}]">
    </li>

    [{else}]

    [{ oxmultilang ident="GREETING" }]
    [{assign var="fullname" value=$oxcmp_user->oxuser__oxfname->value|cat:" "|cat:$oxcmp_user->oxuser__oxlname->value }]
    <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account"}]">
        [{if $fullname}]
        [{ $fullname }]
        [{else}]
        [{ $oxcmp_user->oxuser__oxusername->value|oxtruncate:25:"...":true }]
        [{/if}]
    </a>
    <a id="logoutLink" class="logoutLink" href="[{ $oViewConf->getLogoutLink() }]" title="[{ oxmultilang ident="LOGOUT" }]">[{ oxmultilang ident="LOGOUT" }]</a>

    [{/if}]
    [{if !$oxcmp_user}]

    <li><a id="registerLink" href="[{ oxgetseourl ident=$oViewConf->getSslSelfLink()|cat:"cl=register" }]" title="[{oxmultilang ident="REGISTER"}]">[{oxmultilang ident="REGISTER"}]</a></li>

    [{/if}]
</ul>
[{oxscript widget=$oView->getClassName()}]