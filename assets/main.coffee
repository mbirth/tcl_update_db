document.addEventListener 'DOMContentLoaded', (event) -> 

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

    window.showTooltip = (event) ->
        tt = document.querySelector('#tooltip')
        tt_title = document.querySelector('#tooltip-title')
        tt_text  = document.querySelector('#tooltip-text')

        ref = event.target.parentNode.dataset.ref
        ver = event.target.innerText

        tt_title.innerHTML = ver
        tt_text.innerHTML  = "for #{ref}"


        # Show tooltip
        tt.style.visibility = 'hidden'
        tt.style.display = 'block'
        positionTooltip event.clientX + window.scrollX, event.clientY + window.scrollY
        tt.style.visibility = 'visible'

    window.positionTooltip = (mouseX, mouseY) ->
        tooltip = document.querySelector '#tooltip'

        #pageHeight = document.documentElement.getBoundingClientRect().height
        viewportBottom = document.documentElement.clientHeight + document.documentElement.scrollTop
        viewportRight  = document.documentElement.clientWidth + document.documentElement.scrollLeft
        cursorOffset = 20
        tooltipHeight = tooltip.clientHeight
        tooltipWidth  = tooltip.clientWidth

        #console.log('Cursor: %o, Planned bottom: %o, Viewport bottom: %o', mouseY, mouseY+cursorOffset+tooltipHeight, viewportBottom)

        # Check if tooltip is outside bottom of document
        if mouseY + cursorOffset + tooltipHeight >= viewportBottom
            # show tooltip ABOVE cursor
            tooltip.style.top  = (mouseY - cursorOffset - tooltipHeight) + 'px'
        else
            # show tooltip below cursor
            tooltip.style.top  = (mouseY + cursorOffset) + 'px'

        if mouseX + cursorOffset + tooltipWidth >= viewportRight
            # show tooltip LEFT of cursor
            tooltip.style.left = (mouseX - cursorOffset - tooltipWidth) + 'px'
        else
            # show tooltip right of cursor
            tooltip.style.left = (mouseX + cursorOffset) + 'px'

    versionitems = document.querySelectorAll '.version'
    for vi in versionitems
        vi.addEventListener 'mousemove', (event) ->
            positionTooltip event.clientX + window.scrollX, event.clientY + window.scrollY
        vi.addEventListener 'mouseover', (event) ->
            showTooltip event
        vi.addEventListener 'mouseout', (event) ->
            document.querySelector('#tooltip').style.display = 'none'
