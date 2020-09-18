<section>
    <%-- Breadcrumbs --%>
    <% include BreadCrumbs %>
    <hr>
    <div class="team">
        <header class="main">
            <h1>$Headline.RAW</h1>
        </header>
        <% if $Lead %><p>$Lead.RAW</p><% end_if %>
        <% if $Content %>
            $Content
        <% end_if %>

        <% if $TeamGroups %>
            <% loop $TeamGroups %>
                <% if $PlatoonID == 0 && $SortOrder == 1 %>
                    <div class="row">
                        <div class="col-12-small">
                            <% if $Image %>
                                <a href="$Top.Link$URLSegment/">
                                    <img src="$Image.URL" alt="$Name" title="$Name">
                                </a>
                            <% end_if %>
                            <p class="tac">
                                <a class="button" href="$Top.Link$URLSegment/">$Name.RAW</a>
                            </p>
                        </div>
                    </div>
                <% end_if %>
            <% end_loop %>
        <% end_if %>

        <% if $TeamPlatoons %>
            <% loop $TeamPlatoons %>
                <div class="row">
                    <div class="col-12-small">
                        <% if $Image %>
                            <img src="$Image.URL" alt="$Name" title="$Name">
                        <% end_if %>
                        <h3>$Name.RAW</h3>
                        $Content
                    </div>
                </div>
                <% if $Groups %>
                    <div class="row">
                        <% loop $SortedGroups($PlatoonID) %>
                            <div class="col-6 col-12-small">
                                <% if $Image %>
                                    <a href="$Top.Link$URLSegment/">
                                        <img src="$Image.URL" alt="$Name" title="$Name">
                                    </a>
                                <% end_if %>
                                <p class="tac">
                                    <a class="button" href="$Top.Link$URLSegment/">$Name.RAW</a>
                                </p>
                            </div>
                        <% end_loop %>
                    </div>
                <% end_if %>
            <% end_loop %>
        <% end_if %>

        <% if $TeamGroups %>
            <% loop $TeamGroups %>
                <% if $PlatoonID == 0 && $SortOrder != 1 %>
                    <div class="row">
                        <div class="col-12-small">
                            <% if $Image %>
                                <a href="$Top.Link$URLSegment/">
                                    <img src="$Image.URL" alt="$Name" title="$Name">
                                </a>
                            <% end_if %>
                            <p>
                                <a class="button" href="$Top.Link$URLSegment/">$Name.RAW</a>
                            </p>
                        </div>
                    </div>
                <% end_if %>
            <% end_loop %>
        <% end_if %>
    </div>
</section>




