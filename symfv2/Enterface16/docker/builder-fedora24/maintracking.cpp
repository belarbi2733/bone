#include "opencv2/opencv.hpp"
#include <opencv2/core/core.hpp>
#include <opencv2/highgui/highgui.hpp>
#include <opencv2/video/video.hpp>
#include <opencv2/video/tracking.hpp>
#include <opencv2/imgproc/imgproc.hpp>

#include <stdlib.h>
#include <stdio.h>
#include <ctime>
#include <ctype.h>
#include <string>
#include <iostream>

using namespace std;
using namespace cv;

// Declarations

int LK_step;
int Max_Point;
int pre_filtre;
int median_type;
int max_radius;
int min_radius;
int bin_tresh;
int dist_grav;
int delay;
int frame_counter=0;
clock_t start;
double duration;
bool step_1;
bool step_2;
bool cg=true;
bool contour=true;
bool fps_btn;
bool first_time;
bool save_video;
VideoWriter video;
Mat gray;
Mat prevgray;
Mat image;
Mat gray2;
Mat draw;
Rect ROI;
int video_length;

vector<uchar> status;
vector<float> err;
TermCriteria termcrit;
Size winSize;
vector<Point> calc_grav;
vector<Point2f> points[2];
Point gravity1, gravity2;

void intensity(Mat& image){
	// calcul de l'intensité moyenne
	int counter = 0;
	for (int i = 0; i < image.rows; i++)
	for (int j = 0; j < image.cols; j++)
		counter += (int)image.at<uchar>(i, j);
	counter /= (image.cols * image.rows);

	//Définition du seuil de binarisation et du degré de floutage de l'image
	int median = 0;
	if (counter >= pre_filtre) median = 1;
	medianBlur(image, image, 1 + 2 * median);
	bin_tresh = counter * 4 / 7;
}


void intersect(vector<Point2f> A[], vector<Point>& inter){
	Point2f tmp;
	size_t i = 0, j = 0;

	for (j = 0; j < A[1].size(); j++){

		const float x1 = A[0][j].x;
		const float y1 = A[0][j].y;
		const float x2 = A[1][j].x;
		const float y2 = A[1][j].y;
		const float d21x = x2 - x1, d21y = y2 - y1;

		for (i = j; i < A[1].size(); i++){

			const float x3 = A[0][i].x;
			const float y3 = A[0][i].y;
			const float x4 = A[1][i].x;
			const float y4 = A[1][i].y;
			const float d43x = x4 - x3, d43y = y4 - y3;
			//Intersection à l'infini? <=> parallèle ?
			if (!((d21x)*(d43y)-(d43x)*(d21y))) continue; //(produit vectoriel = 0 ?)

			//Cas particuliers où un vecteur est // à x ou y.
			if (d43x == 0) {
				tmp.x = x1;
				tmp.y = (x1 - x3)*(d43y) / (d43x)+y3;
			}
			else	if (d21x == 0) {
				tmp.x = x3;
				tmp.y = (x3 - x1)*(d21y) / (d21x)+y1;
			}
			else if (d43y == 0) {
				tmp.x = (y1 - y3)*(d43x) / (d43y)+x3;
				tmp.y = y1;
			}
			else if (d21y) {
				tmp.x = (y3 - y1)*(d21x) / (d21y)+x1;
				tmp.y = y3;
			}
			else { //Cas général
				const float a = (d21y) / (d21x);
				const float b = (d43y) / (d43x);
				const float x = (y3 - y1 + x1*a - x3*b) / (a - b);
				tmp.x = x;
				tmp.y = (x - x1)*a + y1;
			}
			inter.push_back(tmp);
		}
	}
}
Point gravity(vector<Point>& points){
	Point2f grav(0, 0);
	for (unsigned i = 0; i < points.size(); i++){
		grav.x += points[i].x;
		grav.y += points[i].y;
	}
	grav.x = grav.x / points.size();
	grav.y = grav.y / points.size();
	return grav;
}
vector<Point> Max_bright(Mat& image, Point& point){
	double angle;
	double radius = 0, x = 0, y = 0;
	const double max_angle = CV_PI / 180 * 359, step_angle = CV_PI / 180, step_rad = 1;
	Point max;
	vector<Point> MaxPoint;

	for (angle = 0; angle < max_angle; angle += step_angle) { //l'angle va balayer un secteur allant de 0° à 359° par pas de 1°
		max.x = point.x;
		max.y = point.y;
		for (radius = min_radius; radius < max_radius; radius += step_rad){
			x = point.x + radius * cos(angle);
			y = point.y + radius * sin(angle);
			if (x < 0 || y < 0 || x >= image.cols || y >= image.rows) continue; //permet de ne pas sortir du cadre de l'image

			// il faut inverser x et y car une matrice se lit d'abord en ligne puis en colonnes
			if (image.at<uchar>((int)y, (int)x) > image.at<uchar>((int)max.y, (int)max.x)) {
				max.x = (float)x;
				max.y = (float)y;
			}
		}
		MaxPoint.push_back(max);
	}
	return MaxPoint;
}
void ROI_on_picture(RotatedRect box, Mat& image){
	Mat mask = image.clone();
	Mat imBis = image.clone();
	mask.setTo(0);
	imBis.setTo(255);
	ellipse(mask, box.center, Size((int)box.size.width / 2, (int)box.size.height / 2), box.angle, 0, 360, 255, CV_FILLED, 8, 0);
	ellipse(imBis, box.center, Size((int)box.size.width / 2, (int)box.size.height / 2), box.angle, 0, 360, 0, CV_FILLED, 8, 0);
	bitwise_and(image, mask, image);
	image = image + imBis;
}
void Draw_Ellipse(Mat& image, Point& point){
	vector<Point> Max;
	RotatedRect box;

	//Etape 'd' : trouver les points du contour, faire passer une ellipse, cette ellipse définit une nouvelle ROI.
	Max = Max_bright(image, point);
	box = fitEllipse(Max);
	ROI_on_picture(box, image);

}
void Binarisation(Mat &image){
	int i, j;
	for (i = 0; i < image.rows; i++)
	for (j = 0; j < image.cols; j++){
		if (image.at<uchar>(i, j) >= bin_tresh) image.at<uchar>(i, j) = 255;
		else image.at<uchar>(i, j) = 0;
	}
}
void Convex_Hull(Mat &frame2){
	vector<vector<Point> > contours;
	vector<Vec4i> hierarchy;
	findContours(frame2, contours, hierarchy, CV_RETR_TREE, CV_CHAIN_APPROX_SIMPLE, Point(0, 0));

	vector<vector<Point> > hull(contours.size());
	frame2.setTo(255);

	int size_max = 0, max = 0;

	for (unsigned i = 0; i < contours.size(); i++) {
		convexHull(Mat(contours[i]), hull[i], false); //calcul des points du contours
		if ((int)hull[i].size() > size_max) { //Permet de sélectionner le contours le plus grand
			max = i;
			size_max = hull[i].size();
		}
	}
	drawContours(frame2, hull, max, 0, 2, 8, vector<Vec4i>(), 0, Point());
}



int main(int argc, char** argv)
{
	//Init variables
	step_1 = true;
	step_2 = false;
	first_time = true;
	frame_counter = 0;
	gravity1 = Point(0, 0);
	gravity2 = Point(0, 0);

	termcrit = TermCriteria(CV_TERMCRIT_ITER | CV_TERMCRIT_EPS, 20, 0.03);
	winSize = Size(31, 31);
	LK_step = 7;
	Max_Point = 100;
	median_type = 9;
	max_radius = 65;
	min_radius = 15;
	bin_tresh = 20;
	dist_grav = 15;

	VideoCapture cap(argv[1]);
	

	if (argc > 2)
		save_video = true;
	else 
		namedWindow("edges", 1);

	video_length = int(cap.get(CV_CAP_PROP_FRAME_COUNT));

	cout << " Vidoe Length" << video_length << endl;

	if (!cap.isOpened())  // check if we succeeded
		return -1;

	Mat edges,image;
	
	while (true)
		{	
		frame_counter++;
		//cout << "compteur :" << frame_counter << endl;

		if (step_1){
		cap.read(image);
		
		cvtColor(image, prevgray, COLOR_BGR2GRAY);
		if (first_time){
		ROI = Rect(prevgray.cols / 6, prevgray.rows / 6, prevgray.cols * 4 / 7, prevgray.rows * 2 / 3);
		video.open("Result.webm", CV_FOURCC('V', 'P', '9', '0'), 25, ROI.size(), false);
		first_time = false;
		}

		prevgray = prevgray(ROI);
		ROI.size();
		intensity(prevgray);
		goodFeaturesToTrack(prevgray, points[0], Max_Point, 0.01, 10, Mat(), 3, 1, 0.04);
		
		cap.read(image);
		if (image.empty()){
			cout << "Fin de la vidéo" << endl;
			return 0;
		}
        
		cvtColor(image, gray, COLOR_BGR2GRAY);
		gray = gray(ROI);
		intensity(gray);

		//Step A : Optical flow computation
		calcOpticalFlowPyrLK(prevgray, gray, points[0], points[1], status, err, winSize, LK_step, termcrit, 0, 0.01);
		//Step b : Computation of intersections
		calc_grav.clear();
		intersect(points, calc_grav);
		//Step C : Definition of gravity center
		gravity1 = gravity(calc_grav);

		if (cg)
			circle(gray, gravity1, 20, 255, 2, 8, 0);

		step_1 = false;
		step_2 = true;

		}

		if (step_2){
		
			gray2 = gray.clone();
			medianBlur(gray2, gray2, 1 + 2 * median_type);
			//Step D : Ellipse definition
			Draw_Ellipse(gray2, gravity1);
			//Step E : Morphological operations
			Binarisation(gray2);
			//Step F : Convex Hull Algorithme(define gravity2)
			Convex_Hull(gray2);
			calc_grav.clear(); //Réinitialisation du vecteur de calcul du centre de gravité
			for (int i = 0; i < gray2.rows; i++)
			for (int j = 0; j < gray2.cols; j++)
			if (gray2.at<uchar>(i, j) == 0) calc_grav.push_back(Point(j, i)); //Détection des pixels du contours
			gravity2 = gravity(calc_grav);

			if (draw.empty())
				draw = Mat(gray2.rows, gray2.cols, CV_8UC1, 255);
			if (contour)
				gray = draw - gray2 + gray;
			if (cg)
				circle(gray, gravity2, 2, 255, 2, 8, 0);
			if (((gravity1.x - gravity2.x) >= dist_grav) || ((gravity1.y - gravity2.y) >= dist_grav)){
				step_1 = true;
				step_2 = false;
			}
			gravity1 = gravity2;

			
			if (save_video)
				video.write(gray);
			else
			    imshow("edges", gray);

			cap.read(image);
			cvtColor(image, gray, COLOR_BGR2GRAY);
			gray = gray(ROI);
			intensity(gray);
		}
		if (frame_counter == (video_length-2)) break;
		//cout << "counter" << frame_counter << endl;
 		if (waitKey(27) >= 0) break;
	}
	cout << "finished" << endl;

	video.release();
	cap.release();
	destroyAllWindows();
	return 0;
}
