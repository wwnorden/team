<section>
    <%-- Breadcrumbs --%>
    <% include BreadCrumbs %>
    <hr>
    <div class="team">
        <% if $Group %>
            <header class="main">
                <h1>$Group.Name.RAW</h1>
            </header>
            <% if $Group.Image %>
                <span>
                    <img src="$Group.Image.URL" alt="$Group.Name" title="$Group.Name">
                </span>
            <% end_if %>
            $Group.Content.RAW
            <% if $Members %>
                <h4><% _t('WWN\Team\TeamMember.PLURALNAME', 'Member') %></h4>
                <div class="row">
                    <% loop $Members %>
                        <div class="col-3 col-12-medium">
                            <img title="$FirstName $LastName"
                                 src="$Image.URL">
                        </div>
                        <div class="col-3 col-12-medium">
                            <ul>
                                <li><strong>$Position</strong></li>
                                <li class="db">$Grade $FirstName $LastName</li>
                                <% if $ShowMailOnSite %>
                                    <li class="db">
                                        <a href="mailto:$Email"><% _t('WWN\Team\TeamMember.db_Email', 'E-mail') %></a>
                                    </li>
                                <% end_if %>
                            </ul>
                        </div>
                    <% end_loop %>
                </div>
            <% end_if %>
            <% if $Vehicles %>
                <h4><strong><% _t('WWN\Team\TeamGroup.has_many_Vehicles', 'Vehicles') %></strong></h4>
                <div class="row">
                    <% loop $Vehicles %>
                        <div class="col-6 col-12-medium">
                            <% loop $VehicleImages %>
                                <a href="$Up.getVehiclePage()/$Up.URLSegment/">
                                    <% if $Cover && $Image %>
                                        <img title="$Up.Name $Up.PagingName"
                                             src="$Image.URL">
                                    <% end_if %>
                                </a>
                            <% end_loop %>
                            <p>
                                <a href="$getVehiclePage()/$URLSegment/" title="$Name $PagingName"
                                   class="button">$Name</a>
                            </p>
                        </div>
                    <% end_loop %>
                </div>
            <% end_if %>
        <% end_if %>
    </div>
</section>
