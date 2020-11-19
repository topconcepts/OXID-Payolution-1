[{$smarty.block.parent}]

[{if $oViewConf->needToDisplayFraudScript()}]
    <script type="text/javascript" src="https://h.online-metrix.net/fp/tags.js?org_id=[{$oViewConf->getFraudPreventionOrgId()}]&session_id=[{$oViewConf->getFraudSessionId()}]">
    </script>
[{/if}]