<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="yes"/>

	<xsl:template match="/eksamid">
		<html>
			<head>
				<title>Exam Details</title>
				<style>
					table {
					width: 100%;
					border-collapse: collapse;
					}
					th, td {
					border: 1px solid black;
					padding: 8px;
					text-align: left;
					}
					th {
					background-color: #f2f2f2;
					}
				</style>

			</head>
			<body>
				<h1>Exam Details</h1>
				<table>
					<tr>
						<th>ID</th>
						<th>Exam Date</th>
						<th>Location</th>
						<th>Examiner Name</th>
						<th>Student Name</th>
						<th>Duration (hrs)</th>
						<th>Email</th>
					</tr>
					<xsl:apply-templates select="eksam"/>
				</table>
			</body>
		</html>
	</xsl:template>

	<xsl:template match="eksam">
		<tr>
			<td>
				<xsl:value-of select="id"/>
			</td>
			<td>
				<xsl:value-of select="eksamiaeg"/>
			</td>
			<td>
				<xsl:value-of select="koht"/>
			</td>
			<td>
				<xsl:value-of select="nimi/eksamineerijanimi"/>
			</td>
			<td>
				<xsl:value-of select="nimi/opilanenimi"/>
			</td>
			<td>
				<xsl:value-of select="kestvus"/>
			</td>
			<td>
				<xsl:value-of select="@email"/>
			</td>
		</tr>
	</xsl:template>
</xsl:stylesheet>