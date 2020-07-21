Sub GetKeywordDataSetRecords(filterValue, context, results)
    Dim http, xml, committees, committee
    Set http = CreateObject("Msxml2.ServerXMLHTTP")
    http.open "GET", "https://bloomington.in.gov/onboard/committees?format=xml", false
    http.send
    Set xml        = http.responseXML
    Set committees = xml.selectNodes("committees/committee")

    For Each committee in committees
        if InStr(committee.selectSingleNode("name").text, filterValue) > 0 Then
            results.BeginRow()
            results.AddData "keyvaluechar", committee.selectSingleNode("name").text
            results.EndRow()
        End If
    Next
End Sub
