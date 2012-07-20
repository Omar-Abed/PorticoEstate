<!-- $Id: view_calendar_year.xsl 9206 2012-04-23 06:21:38Z vator $ -->
<xsl:template match="data"  xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">

	<div id="control_plan">
		<div class="top">
			<h1>Kontrollplan for <xsl:value-of select="control/title"/></h1>
			<h3>Periode: <xsl:value-of select="current_year"/></h3>
			
		</div>
		<div class="middle">
		
			<!-- =====================  COLOR ICON MAP  ================= -->
			<xsl:call-template name="icon_color_map" />
			
			<!-- =====================  CALENDAR NAVIGATION  ================= -->			
			<xsl:call-template name="nav_calendar_year">
    		<xsl:with-param name="view">LOCATIONS_FOR_CONTROL</xsl:with-param>
  		</xsl:call-template>
		</div>
		<div id="cal_wrp">
		<h2>Bygg/Eiendom</h2>
			<table id="calendar" class="year">
				<tr>
					<th class="title">
						<span class="location-code">Lokasjonskode</span>
					</th>
					<th class="title">
						<span class="location-name">Lokasjonsnavn</span>
					</th>
					<xsl:for-each select="heading_array">
						<th>
							<a>
								<xsl:attribute name="href">
									<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_month_for_locations</xsl:text>
									<xsl:text>&amp;year=</xsl:text>
									<xsl:value-of select="//current_year"/>
									<xsl:text>&amp;month=</xsl:text>
									<xsl:number/>
									<xsl:text>&amp;control_id=</xsl:text>
									<xsl:value-of select="//control/id"/>
								</xsl:attribute>
								
								<xsl:variable name="month_str">short_month <xsl:value-of select="."/> capitalized</xsl:variable>
								<xsl:value-of select="php:function('lang', $month_str)" />
							</a>				
						</th>
					</xsl:for-each>
				</tr>
			
			<xsl:choose>
				<xsl:when test="locations_with_calendar_array/child::node()">
				
			  	<xsl:for-each select="locations_with_calendar_array">
			  		<tr>				
						<xsl:choose>
					        <xsl:when test="(position() mod 2) != 1">
					            <xsl:attribute name="class">odd</xsl:attribute>
					        </xsl:when>
					        <xsl:otherwise>
					            <xsl:attribute name="class">even</xsl:attribute>
					        </xsl:otherwise>
					    </xsl:choose>
				    
						<td class="title">
							<xsl:value-of select="location/location_code"/>
						</td>
						<td class="location-name">
							<xsl:value-of select="location/loc1_name"/>
						</td>
							
						<xsl:for-each select="calendar_array">
							<td>
								<xsl:call-template name="check_list_status_manager" >
									<xsl:with-param name="location_code"><xsl:value-of select="//location"/></xsl:with-param>
								</xsl:call-template>
							</td>
						</xsl:for-each>
					</tr>	
				</xsl:for-each>	
			</xsl:when>
			<xsl:otherwise>
				<tr class="cal_info_msg"><td colspan="3"><xsl:value-of select="php:function('lang', 'error_msg_no_controls_in_period')" /></td></tr>
			</xsl:otherwise>
		</xsl:choose>
	</table>

			<table id="calendar" class="year">
				<tr>
					<th class="location-name">
						<span>Lokasjon</span>
					</th>
					<th class="component-type">
						<span>Komponenttype</span>
					</th>
					<xsl:for-each select="heading_array">
						<th>
							<a>
								<xsl:attribute name="href">
									<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_month_for_locations</xsl:text>
									<xsl:text>&amp;year=</xsl:text>
									<xsl:value-of select="//current_year"/>
									<xsl:text>&amp;month=</xsl:text>
									<xsl:number/>
									<xsl:text>&amp;control_id=</xsl:text>
									<xsl:value-of select="//control/id"/>
								</xsl:attribute>
								
								<xsl:variable name="month_str">short_month <xsl:value-of select="."/> capitalized</xsl:variable>
								<xsl:value-of select="php:function('lang', $month_str)" />
							</a>				
						</th>
					</xsl:for-each>
				</tr>
				
			<h2 class="components">Komponenter</h2>
			
			<xsl:choose>
				<xsl:when test="components_with_calendar_array/child::node()">
				
			  	<xsl:for-each select="components_with_calendar_array">
			  		<tr>				
						<xsl:choose>
					        <xsl:when test="(position() mod 2) != 1">
					            <xsl:attribute name="class">odd</xsl:attribute>
					        </xsl:when>
					        <xsl:otherwise>
					            <xsl:attribute name="class">even</xsl:attribute>
					        </xsl:otherwise>
					    </xsl:choose>
				    
						<td class="location-name">
							<xsl:value-of select="component/xml_short_desc"/>
						</td>
						<td class="component-type">
							<xsl:value-of select="component/type_str"/>
						</td>
							
						<xsl:for-each select="calendar_array">
							<td>
								<xsl:call-template name="check_list_status_manager" >
									<xsl:with-param name="location_code"><xsl:value-of select="//location"/></xsl:with-param>
								</xsl:call-template>
							</td>
						</xsl:for-each>
					</tr>	
				</xsl:for-each>	
			</xsl:when>
			<xsl:otherwise>
				<tr class="cal_info_msg"><td colspan="3"><xsl:value-of select="php:function('lang', 'error_msg_no_controls_in_period')" /></td></tr>
			</xsl:otherwise>
		</xsl:choose>
	</table>
	</div>
</div>
</div>
</xsl:template>
