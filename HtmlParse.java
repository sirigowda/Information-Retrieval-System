import java.io.File;
import java.io.BufferedWriter;
import java.io.FileInputStream;
import java.io.FileWriter;
import java.io.IOException;

import org.apache.tika.exception.TikaException;
import org.apache.tika.language.LanguageIdentifier;
import org.apache.tika.metadata.Metadata;
import org.apache.tika.parser.ParseContext;
import org.apache.tika.parser.html.HtmlParser;
import org.apache.tika.sax.BodyContentHandler;

import org.xml.sax.SAXException;

public class HtmlParse {

	public final String baseFilePath = "/home/siri/Downloads/ABCNewsData/ABCNewsDownloadData/";
	@SuppressWarnings("deprecation")
	public static void main(final String[] args) throws IOException,SAXException, TikaException {

		File folder_name = new File(baseFilePath);
		File[] listOfFiles = folder_name.listFiles();
		String outputFileName = "big.txt";
		File outputFile = new File(outputFileName);
		BufferedWriter writer = new BufferedWriter(new FileWriter(outputFile));


		for (File file : listOfFiles) {
			if (file.isFile() && file.getName().contains(".html")) {
				System.out.println(file.getName());
				FileInputStream inputstream = new FileInputStream(new File(baseFilePath+file.getName()));
				//detecting the file type
				BodyContentHandler handler = new BodyContentHandler(-1);
				Metadata metadata = new Metadata();
				ParseContext pcontext = new ParseContext();
				//Html parser 
				
				HtmlParser htmlparser = new HtmlParser();
				htmlparser.parse(inputstream, handler, metadata,pcontext);
				//System.out.println("Contents of the document:" + handler.toString());
				LanguageIdentifier identifier = new LanguageIdentifier(handler.toString());
			    if(identifier.getLanguage().equals("en"))
			    {
				String content=handler.toString().trim();
				//content=content.replaceAll("[\\\n]+","");
				content=content.replaceAll("[^a-zA-Z0-9 ]+","");
				content=content.replaceAll("[  ]+"," ");				
				writer.write(content);
			    }
			}
		}
		writer.flush();
		writer.close();
	}
}
