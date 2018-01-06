window.mdc.autoInit()
window.tabBar = new mdc.tabs.MDCTabBar document.querySelector '#tab-bar'

# Hide all panels but the selected one
activatePanel = (panelId) ->
    allPanels = document.querySelectorAll '.panel'
    for panel, i in allPanels
        if panel.id is panelId
            tabBar.activeTabIndex = i
        panel.style.display = if panel.id is panelId then 'block' else 'none'

# React to clicking the tabs
window.tabBar.listen 'MDCTabBar:change', (t) ->
    nthChildIndex = t.detail.activeTabIndex
    tabId = t.srcElement.id
    tab = document.querySelector "##{tabId} .mdc-tab:nth-child(#{nthChildIndex + 1})"
    panelId = tab.dataset.panel
    activatePanel panelId

# If specific tab/panel given in URL, e.g. #motion, switch to that
hash = location.hash
if hash.length > 1
    activatePanel 'family-' + hash.substring 1
else
    activatePanel 'family-keyone'
