import java.io.*;
import java.util.*;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.net.URL;
import java.nio.charset.Charset;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class DataSetReader {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		
		 File input = new File("C:/Users/Lighter/Downloads/Webscope_C15/ydata-ymusic-kddcup-2011-track1/ydata-ymusic-kddcup-2011-track1/trainIdx1.txt");
		 File tableInput = new File("doc/table.txt");
		 File artistInput = new File("doc/artists.txt");
		 
		 
		 ArrayList<ArtistRating> artistTable = new ArrayList<ArtistRating>();
		 ArrayList<String> artists = new ArrayList<String>(3000);
		 ArrayList<Integer> compareArts = new ArrayList<Integer>(4);
		 ArrayList<String> searchArtists = new ArrayList<String>();
		 ArrayList<String> artistsGot = new ArrayList<String>();
		 ArrayList<String> usersDone = new ArrayList<String>();
		 
		 try {
			 
			Scanner asc = new Scanner(tableInput);
			int count = 0;
			while(asc.hasNext()) {
				if(count == 0) {
					String[] arts = asc.nextLine().split("\t");
					for(int i = 1; i < arts.length; i++) {
						artists.add(arts[i]);
						ArtistRating newArtist = new ArtistRating(arts[i]);
						artistTable.add(newArtist);
					}
				}else {
					artistTable.get(count-1).addRatingLine(asc.nextLine());
				}
				count++;
			}
			
			Scanner agsc = new Scanner(new File("doc/artistsGot.txt"));
			while(agsc.hasNextLine()) {
				artistsGot.add(agsc.nextLine());
			}
			
			Scanner sasc = new Scanner(new File("doc/searchArtists.txt"));
			while(sasc.hasNextLine()) {
				String newArtist = sasc.nextLine();
				if(!artistsGot.contains(newArtist)) {
					searchArtists.add(newArtist);
				}
			}
			
			Scanner usc = new Scanner(new File("doc/usersDone.txt"));
			while(usc.hasNextLine()) {
				String newUser = usc.nextLine();
				if(!artistsGot.contains(newUser)) {
					usersDone.add(newUser);
				}
			}
			
			for(int n = 0; n < 64; n++) {
				System.out.println(n);
				ArrayList<String> users
				= new ArrayList<String>();  
				JSONObject json = readJsonFromUrl("http://ws.audioscrobbler.com/2.0/?format=json&method=artist.gettopfans&artist=" + searchArtists.get(n) + "&api_key=b25b959554ed76058ac220b7b2e0a026");
				if(json != null) {
					JSONObject topfans = (JSONObject) json.get("topfans");
					JSONArray user = (JSONArray) topfans.get("user");
					for(int i = 0; i < user.length(); i++) {
						JSONObject us = user.getJSONObject(i);
						users.add(us.get("name").toString());
					}
				}
				
				
				
				for(int i = 0; i < users.size(); i++) {
					if(!usersDone.contains(users.get(i))) {
						System.out.println(users.get(i));
						try {
							JSONObject userJson = readJsonFromUrl("http://ws.audioscrobbler.com/2.0/?format=json&method=user.gettopartists&user=" + users.get(i) + "&api_key=b25b959554ed76058ac220b7b2e0a026");
							if(userJson != null) {	
								if(userJson.has("topartists")) {
									JSONObject topArtists = userJson.getJSONObject("topartists");
									if(topArtists.optJSONArray("artist") != null) {
										JSONArray arts = topArtists.getJSONArray("artist");
										for(int j = 0; j < arts.length(); j++) {
											String name = arts.getJSONObject(j).get("name").toString();
											Pattern p = Pattern.compile("[^a-z0-9[&!-] ]", Pattern.CASE_INSENSITIVE);
											Matcher m = p.matcher(name);
											if(!m.find()) {
												System.out.println(name);
												if(!searchArtists.contains(name) && !artistsGot.contains(name)) {
													searchArtists.add(name);
												}
												int index = artists.indexOf(name);
												// kijken of de artiest zich in de tabel bevindt en zo niet dan toevoegen
												if(index == -1) {
													artists.add(name);
													index = artists.indexOf(name);
													ArtistRating newArtist = new ArtistRating(name);
													artistTable.add(index,newArtist);
													for(int l = 0; l < artists.size(); l++) {
														artistTable.get(index).addRating(l,0);
														artistTable.get(l).addRating(index,0);
													}
												}
												// na toevoegen nu tijd om de rating te verwerken
												for(int k = 0; k < compareArts.size(); k++) {
													int rating = 3 - k;
													int oldRating = artistTable.get(compareArts.get(k)).getRatings().get(index);
													int newRating = oldRating + rating;
													artistTable.get(compareArts.get(k)).getRatings().set(index, newRating);
													oldRating = artistTable.get(index).getRatings().get(compareArts.get(k));
													newRating = oldRating + rating;
													artistTable.get(index).getRatings().set(compareArts.get(k), newRating);
												}
												compareArts.add(index);
												if(compareArts.size() >= 4) {
													compareArts.remove(0);
												}
											}
										}
									}
								}
							}
						} catch(Exception e) {
							e.printStackTrace();
						}
						compareArts = new ArrayList<Integer>();
					}
					usersDone.add(users.get(i));
				}
				artistsGot.add(searchArtists.get(n));
				System.out.println(n + "-out");
			}
			
			System.out.println(artistTable.size());
			
			 FileWriter fstream = new FileWriter("doc/table2.txt");
			 BufferedWriter out = new BufferedWriter(fstream);
			 String printLine = "null" + "\t";
			 for(int i = 0; i < artists.size(); i++) {
				 printLine += artists.get(i) + "\t";
			 }
			 out.write(printLine + "\n");
			 
			 for(int i = 0; i < artistTable.size(); i++) {
				 ArrayList<Integer> line = artistTable.get(i).getRatings();
				 printLine = artistTable.get(i).getName() + "\t";
				 for(int j = 0; j < line.size(); j++) {
					printLine += line.get(j) + "\t";
				 }
				 out.write(printLine + "\n");
			 }
			 
			 out.close();
			 
			 FileWriter sAstream = new FileWriter("doc/searchArtists.txt");
			 BufferedWriter sAout = new BufferedWriter(sAstream);
			 for(int i = 0; i < searchArtists.size(); i++) {
				 sAout.write(searchArtists.get(i) + "\n");
			 }
			 
			 sAout.close();
			 
			 FileWriter aGstream = new FileWriter("doc/artistsGot.txt");
			 BufferedWriter aGout = new BufferedWriter(aGstream);
			 for(int i = 0; i < artistsGot.size(); i++) {
				 aGout.write(artistsGot.get(i) + "\n");
			 }
			 
			 aGout.close();
			 
			 FileWriter ustream = new FileWriter("doc/usersDone.txt");
			 BufferedWriter uout = new BufferedWriter(ustream);
			 for(int i = 0; i < usersDone.size(); i++) {
				 uout.write(usersDone.get(i) + "\n");
			 }
			 
			 uout.close();
			 
		 }catch(Exception e) {
			 e.printStackTrace();
		 }
		 
		 
	}
	
	private static String readAll(Reader rd) throws IOException {
	    StringBuilder sb = new StringBuilder();
	    int cp;
	    while ((cp = rd.read()) != -1) {
	      sb.append((char) cp);
	    }
	    return sb.toString();
	  }

	  public static JSONObject readJsonFromUrl(String url) throws IOException, JSONException {
	    try {
	      InputStream is = new URL(url).openStream();
	      BufferedReader rd = new BufferedReader(new InputStreamReader(is, Charset.forName("UTF-8")));
	      String jsonText = readAll(rd);
	      JSONObject json = new JSONObject(jsonText);
	      is.close();
	      return json;
	    } catch(Exception e) {
	      return null;
	    }
	  }
	
	

}
