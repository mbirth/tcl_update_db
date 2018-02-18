document.addEventListener 'DOMContentLoaded', (event) -> 

    window.drawer = new mdc.drawer.MDCTemporaryDrawer document.querySelector '.mdc-drawer'
    document.querySelector('.mdc-toolbar__menu-icon').addEventListener 'click', ->
        drawer.open = true
