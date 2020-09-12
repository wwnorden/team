<section>
    <%-- Breadcrumbs --%>
    <% include BreadCrumbs %>
    <hr>
    <div class="team">
        <header class="main">
            <h1>$Headline.RAW</h1>
        </header>
        <% if $Group %>
            <h3>$Group.Name.RAW</h3>
            <p>$Group.Platoon.RAW</p>
            <% if $Group.Image %>
                <div class="row">
                    <div class="col-12-small">
                        <img src="$Group.Image.URL" alt="$Group.Name" title="$Group.Name">
                    </div>
                </div>
            <% end_if %>
            <% if $Vehicles %>
                <p><strong>Vehicles</strong></p>
                <ul>
                    <% loop $Vehicles %>
                        <li>
                            <a href="$getVehiclePage()/$URLSegment/" title="$Name $PagingName"
                               class="button">$Name</a>
                        </li>
                    <% end_loop %>
                </ul>
            <% end_if %>

            <% if $Members %>
                <p><strong>Member</strong></p>
                <ul>
                    <% loop $Members %>
                        $Grade
                        $FirstName
                        $LastName
                        <% if $ShowMailOnSite %>
                            <a href="mailto:$Email"><% _t('WWN\Team\TeamMember.db_Email', 'E-mail') %></a>
                        <% end_if %>
                        $Position
                    <% end_loop %>
                </ul>
            <% end_if %>
        <% end_if %>
    </div>
</section>
