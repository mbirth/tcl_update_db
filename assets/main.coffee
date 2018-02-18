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
        tt = document.querySelector '#tooltip'
        tt_title = document.querySelector '#tooltip-title'
        tt_text  = document.querySelector '#tooltip-text'

        ref = event.target.parentNode.dataset.ref
        ver = event.target.innerText

        meta = window.metadata[ref]
        #console.log("Meta: %o", meta)
        vermeta = meta['versions'][ver]

        updateText = ''
        if vermeta['FULL'].length > 0
            fullmeta = vermeta['FULL'][0]
            fullInfo = "✔️ (first released: #{fullmeta['published_first']})"
            if fullmeta['note']['en']
                updateText = fullmeta['note']['en']
                updateText = updateText.replace /\n/g, '<br/>'
                #updateText += '<br/><br/>'
        else
            fullInfo = "❌"

        if vermeta['OTA'].length > 0
            fromList = (v['fv'] for v in vermeta['OTA'])
            fromList = fromList.sort().reverse()
            fromListText = fromList.join ', '
            if fromList.length > 3
                fromMore = fromList.length - 3
                fromList = fromList[0..2]
                fromListText = (fromList.join ', ') + " + #{fromMore} more"
            otaInfo = "✔️ (from #{fromListText})"
        else
            otaInfo = "❌"

        if vermeta['OTA_FROM'].length > 0
            toList = (v['tv'] for v in vermeta['OTA_FROM'])
            toList = toList.sort().reverse()
            toListText = toList.join ', '
            if toList.length > 3
                toMore = toList.length - 3
                toList = toList[0..2]
                toListText = (toList.join ', ') + " + #{toMore} more"
            updateInfo = "<strong>OTA possible to #{toListText}</strong>"
        else
            updateInfo = "No OTA to future version."

        tt_title.innerHTML = ver
        tt_text.innerHTML  = """
            for #{ref}<br/>
            #{meta['variant']}<br/>
            <br/>
            FULL: #{fullInfo}<br/>
            OTA: #{otaInfo}<br/>
            #{updateInfo}<br/>
            <br/>
            #{updateText}
        """


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

    # Load metadata
    metadata = {}
    xhr = new XMLHttpRequest()
    xhr.open('GET', 'json_updatedetails.php', true);
    xhr.onreadystatechange = ->
        if xhr.readyState is 4 and xhr.status is 200
            window.metadata = JSON.parse xhr.responseText

            # Add event listeners to all versions so tooltips start to work AFTER data was loaded
            versionitems = document.querySelectorAll '.version'
            for vi in versionitems
                vi.addEventListener 'mousemove', (event) ->
                    positionTooltip event.clientX + window.scrollX, event.clientY + window.scrollY
                vi.addEventListener 'mouseover', (event) ->
                    showTooltip event
                vi.addEventListener 'mouseout', (event) ->
                    document.querySelector('#tooltip').style.display = 'none'

            snackbar = new mdc.snackbar.MDCSnackbar document.querySelector '.mdc-snackbar'
            snackbar.show
                message: 'Update details loaded. Hover a version number to see details.'
                timeout: 5000

    xhr.send()
