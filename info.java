import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.HashMap;
import java.util.HashSet;

public class info {

        static HashMap<String, String> urlFileMap = new HashMap<String, String>();
        static HashMap<String, String> FileURLMap = new HashMap<String, String>();
        
        public final String project_directory="/home/siri/Downloads/ABCNewsData/";
        
        public static void fetchFieNameExcel(){
                String csvFile = project_directory + "ABCNewsDownloadData/mapABCNewsDataFile.csv";
                BufferedReader br = null;
                String line = "";
                String cvsSplitBy = ",";

                try {
                        br = new BufferedReader(new FileReader(csvFile));
                        while ((line = br.readLine()) != null) {
                                // use comma as separator
                                String[] fileName = line.split(cvsSplitBy);
                                urlFileMap.put(fileName[1], fileName[0]);
                                FileURLMap.put(fileName[0], fileName[1]);
                        }
                } catch (FileNotFoundException e) {
                        e.printStackTrace();
                } catch (IOException e) {
                        e.printStackTrace();
                } finally {
                        if (br != null) {
                                try {
                                        br.close();
                                } catch (IOException e) {
                                        e.printStackTrace();
                                }
                        }
                }

        }

        public static void main(String[] args) throws Exception{
                fetchFieNameExcel();
                HashSet<String> edges = new HashSet<String>();
                String outputFileName = project_directory + "edgeList.txt";
                File outputFile = new File(outputFileName);
                BufferedWriter writer = new BufferedWriter(new FileWriter(outputFile));
                File dirPath = new File(project_directory + "ABCNewsDownloadData/");

                for(File fileEntry: dirPath.listFiles()){
                        Document doc = Jsoup.parse(fileEntry,"UTF-8", FileURLMap.get(fileEntry.getName()));

                        Elements links = doc.select("a[href]");
                        Elements media = doc.select("[src]");
                        for(Element link: links){
                                String url = link.attr("abs:href").trim();
                                if(urlFileMap.containsKey(url)){
                                	edges.add(fileEntry.getName() + " " + urlFileMap.get(url));
                                }
                        }
                }
                int count = 0;
                for(String s: edges){
                        writer.write(s);
                        writer.newLine();
                        System.out.println(count);
                        count ++;
                }

                System.out.println("Successfully written. Total count: " + count);
                writer.flush();
                writer.close();
        }
}
