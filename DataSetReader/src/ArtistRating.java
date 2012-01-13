import java.util.*;

public class ArtistRating {
	
	private String name;
	private ArrayList<Integer> ratings = new ArrayList<Integer>();
	
	
	public ArtistRating(String nm) {		
		name = nm;
	}
	
	public void addRatingLine(String line) {
		String[] lineArray = line.split("\t");
		for(int i = 1; i < lineArray.length; i++) {
			ratings.add(Integer.parseInt(lineArray[i]));
		}
	}
	
	public String getName() {
		return name;
	}
	
	public ArrayList<Integer> getRatings() {
		return ratings;
	}
	
	public void addRating(int position,int value) {
		ratings.add(position,value);
	}
	
}
