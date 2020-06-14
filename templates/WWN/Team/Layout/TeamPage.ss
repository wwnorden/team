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
            <div class="row">
                <% loop $TeamGroups %>
                    <div class="col-6 col-12-small">
                        <a href="$Top.Link$URLSegment/">
                            <h3 class="tac">$Name.RAW</h3>
                            <% if $Image %>
                                <img src="$Image.URL" alt="$Name" title="$Name">
                            <% end_if %>
                        </a>
                    </div>
                <% end_loop %>
            </div>
        <% end_if %>
    </div>
</section>




