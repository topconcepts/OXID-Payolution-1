[{$smarty.block.parent}]
[{if $oViewConf->needToDisplayFraudScript()}]
    <noscript>
        <iframe style="width: 100px; height: 100px; border: 0; position: absolute; top: -5000px;"
                src="https://h.online-metrix.net/fp/tags?org_id=[{$oViewConf->getFraudPreventionOrgId()}]&session_id=[{$oViewConf->getFraudSessionId()}]">
        </iframe>
    </noscript>
[{/if}]