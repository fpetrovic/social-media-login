<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<style>
    @charset "UTF-8";
    /* CSS Document */
    body {
        width: 100px;
        height: 100px;
        background: -webkit-linear-gradient(90deg, #16222A 10%, #3A6073 90%); /* Chrome 10+, Saf5.1+ */
        background: -moz-linear-gradient(90deg, #16222A 10%, #3A6073 90%); /* FF3.6+ */
        background: -ms-linear-gradient(90deg, #16222A 10%, #3A6073 90%); /* IE10 */
        background: -o-linear-gradient(90deg, #16222A 10%, #3A6073 90%); /* Opera 11.10+ */
        background: linear-gradient(90deg, #16222A 10%, #3A6073 90%); /* W3C */
        font-family: 'Raleway', sans-serif;
    }

    p {
        color: #CCC;
    }

    .spacing {
        padding-top: 7px;
        padding-bottom: 7px;
    }

    .middlePage {
        width: 680px;
        height: 500px;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
    }

    .logo {
        color: #CCC;
    }
</style>


[{*[{oxscript include="js/widgets/oxloginbox.js" priority=10 }]
[{oxscript add="$( '#loginBoxOpener' ).oxLoginBox();"}]
[{assign var="bIsError" value=0 }]
[{capture name=loginErrors}]
    [{foreach from=$Errors.loginBoxErrors item=oEr key=key }]
        <p id="errorBadLogin" class="errorMsg">[{ $oEr->getOxMessage()}]</p>
        [{assign var="bIsError" value=1 }]
    [{/foreach}]
[{/capture}]*}]

[{if $oxcmp_user->oxuser__oxpassword->value}]
    [{$oView->redirectLoggedUser()}]
[{else}]

<link href='http://fonts.googleapis.com/css?family=Raleway:500' rel='stylesheet' type='text/css'>

<body>
<div class="middlePage">
    <div class="page-header">
        [{assign var="shop" value = $oView->getCurrentShop() }]
        <h1 class="logo">[{$shop->oxshops__oxname->value}]<br/>
            <small>[{$shop->oxshops__oxstarttitle->value}]</small>
        </h1>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Please Sign In</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="altLoginBox corners clear">
                        <span>[{ oxmultilang ident="LOGIN_WITH" suffix="COLON" }]</span>
                        [{assign var="aActSocialMedia" value = $oView->getActivatedSocialMediaServices() }]
                        [{foreach from=$aActSocialMedia item=oSocialMediaService  }]
                        [{assign var="buttonTpl" value =$oSocialMediaService->sLoginButtonTpl }]

                        [{include file=$buttonTpl}]
                        [{/foreach}]
                    </div>
                </div>
                <div class="col-md-7" style="border-left:1px solid #ccc;height:160px">
                    <form class="form-horizontal" name="login" action="[{ $oViewConf->getSslSelfLink() }]"
                          method="post">
                        <fieldset>
                            [{ $oViewConf->getHiddenSid() }]
                            [{ $oViewConf->getNavFormParams() }]

                            <input type="hidden" name="fnc" value="login">
                            <input type="hidden" name="cl" value="[{ $oViewConf->getTopActiveClassName() }]">

                            <input type="hidden" name="CustomError" value="loginBoxErrors">
                            <input id="loginEmail" name="lgn_usr" type="text" placeholder="Enter your email"
                                   class="form-control input-md">

                            <input id="loginPasword" name="lgn_pwd" type="password" placeholder="Enter Password"
                                   class="form-control input-md">
                            [{$smarty.capture.loginErrors}]
                            [{if $oViewConf->isFunctionalityEnabled( "blShowRememberMe" )}]
                            <div class="spacing"><input type="checkbox" name="lgn_cook" id="remember" value="1">
                                <small><label for="remember">[{ oxmultilang ident="REMEMBER_ME" }]</label></small>
                            </div>
                            [{/if}]
                            <div class="forgotPasswordOpener"><a href="#">
                                    <small> Forgot Password?</small>
                                </a><br/></div>
                            <button type="submit" class="submitButton btn btn-info btn-sm pull-right">[{ oxmultilang
                                ident="LOGIN" }]
                            </button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
    [{/if}]