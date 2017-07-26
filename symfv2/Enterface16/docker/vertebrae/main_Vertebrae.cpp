/*
* File:   main.cpp
* Author: amine
*
* Created on November 17, 2013, 1:17 PM
*/

/*
* File:   main.cpp
* Author: amine
*
* Created on June 27, 2013, 10:01 AM
*/


#include "opencv2/highgui/highgui.hpp"
#include "opencv2/imgproc/imgproc.hpp"
#include "opencv2/core/core.hpp"
#include "opencv/highgui.h"
#include "opencv/cv.h"
#include <iostream>
#include <stdio.h>
#include <stdlib.h>
#include <cstdlib> //exit
#include <ctime>
#include <fstream> //file creation
#include <string>

using namespace cv;
using namespace std;

Mat threshold_output;
Mat threshold_output2;

stringstream ss;
stringstream ss1;
stringstream ss2;

string folderName = "Result_";
string fullPath, fullPath1, fullPath2;
string V, V1, V2;

int tailleX, tailleY;
Mat src, weighted, weighted2, img_clust, courbe, src_gray;
Mat src_kmeans;
int thresh = 65;
int max_thresh = 255;
RNG rng(12345);
Mat drawing,drawing2;
Mat k_img, g_img;
Mat curveNew;
int beginCurve, endCurve;
//openclose
//the address of variable which receives trackbar position update

int max_iters = 10;
int open_close_pos = 7;
int element_shape = MORPH_ELLIPSE;
Mat dst, dst1;
char* instr;
bool saving=false;

//canny function
int edgeThresh = 29;
int lowThreshold = 29;
int highThreshold;
int const max_lowThreshold = 100;
int const max_highThreshold = 400;
int ratio = 3;
int kernel_size = 3;
int TH;

//Roi selection 
Point point1, point2; /* vertical points of the bounding box */
int drag = 0;
Rect rect; /* bounding box */
Mat img, roiImg; /* roiImg - the part of the image in the bounding box */
int select_flag = 0;

//FloodFill
Mat image0, image, gray, mask, mask_hist, metas;
int ffillMode = 1;
int loDiff = 20, upDiff = 20;
int connectivity = 4;
int isColor = true;
bool useMask = false;
int newMaskVal = 255;

// profiling

clock_t start, stop;
//double time;


RotatedRect missedV;

struct pointz {
	float value;
	int x;
	int y;
};

struct point {
	float x;
	float y;
	bool active;
};

//GHT
Mat tabledraw;
int tailleScale = 6;
int pasPhi = 10;
int tailleAngle = (90 / pasPhi) + 1;
//int tailleAngle=10;
//int tailleX, tailleY;
int bords = 10;
int refPointX, refPointY; //points référence modèle
bool ON;
int tableThresh = 42;
//fonction ordonner

bool ordonner(RotatedRect po1, RotatedRect po2) {
	return po1.center.y < po2.center.y;
}
;

//Coefficient du Polynome
double a0, a1, a2, a3, a4, a5, a6, a7, a8, a9;
double b0, b1, b2, b3, b4, b5, b6, b7, b8, b9;
//  On crée proprement le tableau de vote, cf fonctions SetValue, IncrementValue, GetValue
unsigned char* tab;
//Scale et angle sont des entiers correpondant à des indices dans le tableau, cette fonction permets de récupérer la valeur réele de ces facteurs.

float getS(int scale) {
	return float (0.8 + 0.1 * scale);
}

float getAD(int angle) {
	return float((-9 + angle) * 10);
}

/*float getAR(int angle) {
	return (-9 + angle)*M_PI / 18;
}*/
//Le tableau à 4 dimentioGetValuens est géré dans un seul, pour éviter le gaspillage de mémoire du aux pointeurs de pointeurs de pointeurs...

unsigned char GetValue(unsigned char* tab, int a, int b, int c, int d) {
	return tab[d + (tailleScale * c) + (tailleScale * tailleAngle * b) + (tailleScale * tailleAngle * tailleY) * a];
}

void SetValue(unsigned char* tab, int a, int b, int c, int d, unsigned char value) {
	tab[d + (tailleScale * c) + (tailleScale * tailleAngle * b) + (tailleScale * tailleAngle * tailleY) * a] = value;
}

void IncrementValue(unsigned char* tab, int a, int b, int c, int d) {
	tab[d + (tailleScale * c) + (tailleScale * tailleAngle * b) + (tailleScale * tailleAngle * tailleY) * a]++;
}

//Décris un couple de données, tels qu'ils sont utilisés pour l'analyse du modèle

struct couple {
	float beta;
	float r;
};

//Définition de la R-table :
//int pasPhi=10;
vector < vector <couple> > RTable((180 / pasPhi) + 1);

/// Function header
void thresh_callback(int, void*);
void Kmeans();
void FloofFill(Mat image0);
void Kmeans2(Mat src_km);
Mat OpenClose2(Mat src_opec);
void chargementModele(string url);
void accumulator(Mat src, Mat canny);
void tableDraw(unsigned char* tab, Mat tableDraw);
void afficherModele();
//fonction qui génère la courbe des vertèbres
void polyRegression(vector<RotatedRect> matrixRec);
void polynomialFitting(vector<RotatedRect> pointcourbe);
void polynomialFitting(vector<RotatedRect> pointcourbe2);
void grayProfile();
int poly(int yc);
//file stream
void file(vector <int> gg);

void grid(Mat profile, vector <pointz> profile_vec);



//======================================================================
// Functions definition
//======================================================================

/** @function thresh_callback */


void thresh_callback(int, void*) {

	vector<vector<Point> > contours;
	vector<Vec4i> hierarchy;

	/// Detect edges using Threshold

//	cout << "thresh" << thresh << endl;

	cout << "TH" << TH << endl;


	threshold(src_gray, threshold_output, TH, 255, THRESH_BINARY);
	//imshow("thresh", threshold_output);

	//cvtColor( threshold_output, grayImg, CV_8U);
	// threshold_output.copyTo(grayImg);

	/// Find contours
	findContours(threshold_output, contours, hierarchy, CV_RETR_TREE, CV_CHAIN_APPROX_SIMPLE, Point(0, 0));

	// findContours(dst1, contours, hierarchy, CV_RETR_TREE, CV_CHAIN_APPROX_SIMPLE, Point(0, 0));       
	// Find the rotated rectangles and ellipses for each contour
	vector<RotatedRect> minRect(contours.size());
	vector<RotatedRect> minEllipse(contours.size());
	vector<RotatedRect> pointCourbe;
	vector<RotatedRect> pointCourbe2;

	for (int i = 0; i < contours.size(); i++) {
		minRect[i] = minAreaRect(Mat(contours[i]));
		if (contours[i].size() > 5) {
			minEllipse[i] = fitEllipse(Mat(contours[i]));
		}
	}

	/// Draw contours + rotated rects + ellipses
	drawing = Mat::zeros(threshold_output.size(), CV_8UC3);
	drawing2 = Mat::zeros(threshold_output.size(), CV_8UC3);

	for (int i = 0; i < contours.size(); i++) {
		Scalar color = Scalar(rng.uniform(0, 255), rng.uniform(0, 255), rng.uniform(0, 255));

		if (minEllipse[i].size.height > 15 && minEllipse[i].size.width > 15 && minEllipse[i].size.height < 40 && minEllipse[i].size.width < 40) {

			pointCourbe.push_back(minEllipse[i]);
			pointCourbe2.push_back(minEllipse[i]);

			// contour
			drawContours(drawing, contours, i, color, CV_FILLED, 4, vector<Vec4i >(), 0, Point());
			// drawContours(drawing, contours, i, color, 1, 4, vector<Vec4i > (), 0, Point());
			//circle(drawing,minEllipse[i].center,2,color,2,8);

			// ellipse
			//  ellipse( drawing, minEllipse[i], color, 2, 8 );   

			// rotated rectangle
			Point2f rect_points[4];
			minRect[i].points(rect_points);
			for (int j = 0; j < 4; j++)
				line(drawing2, rect_points[j], rect_points[(j + 1) % 4], color, 1, 8);

		}

	}

	sort(pointCourbe.begin(), pointCourbe.end(), ordonner);
	//sort(pointCourbe2.begin(), pointCourbe2.end(), ordonner);
	char c[2];

	for (int i = 0; i < pointCourbe.size(); i++) {
		circle(drawing, pointCourbe[i].center, 2, CV_RGB(0, 255, 0), 2, 8);
		circle(drawing2, pointCourbe[i].center, 2, CV_RGB(0, 255, 0), 2, 8);
		sprintf(c, "%i", i);
		putText(drawing, c, Point(int(pointCourbe[i].center.x + 5), int(pointCourbe[i].center.y)), FONT_HERSHEY_PLAIN, 1, CV_RGB(0, 255, 0), 1, 4);
		putText(drawing2, c, Point(int(pointCourbe[i].center.x + 5), int(pointCourbe[i].center.y)), FONT_HERSHEY_PLAIN, 1, CV_RGB(0, 255, 0), 1, 4);
		circle(drawing2, pointCourbe[i].center, 2, CV_RGB(0, 255, 0), 2, 8);
	}





	// Principal functions

	polynomialFitting(pointCourbe);
	//polynomialFitting(pointCourbe2);
	//grayProfile();
	addWeighted(drawing, 0.7, src, 0.9, 0.0, weighted);
	addWeighted(drawing2, 0.7, src, 0.9, 0.0, weighted2);

	if (saving)
	{
		//imwrite("MRI_vertebrae.jpg", weighted2);
		//imwrite("MRI_segmented_vertebrae.jpg", weighted);
		
		imwrite(fullPath1, weighted);
		imwrite(fullPath2, weighted2);
	}
		
	else {
		imshow("Contours", weighted);
		imshow("Contours2", weighted2);
	}
}

void Kmeans() {

	Mat samples(src.rows * src.cols, 3, CV_32F);
	src.copyTo(src_kmeans);

	blur(src_kmeans, src_kmeans, Size(3, 3));

	Mat element = getStructuringElement(element_shape, Size(3, 3));
	morphologyEx(src_kmeans, src_kmeans, CV_MOP_OPEN, element);

	//imshow(" Src Km ", src_kmeans);

	for (int y = 0; y < src_kmeans.rows; y++)
	for (int x = 0; x < src_kmeans.cols; x++)
	for (int z = 0; z < 3; z++)
		samples.at<float>(y + x * src_kmeans.rows, z) = src_kmeans.at<Vec3b >(y, x)[z];


	int clusterCount = 3;
	Mat labels;
	int attempts = 10;
	Mat centers;
	kmeans(samples, clusterCount, labels, TermCriteria(CV_TERMCRIT_ITER | CV_TERMCRIT_EPS, 0, 10000), attempts, KMEANS_PP_CENTERS, centers);


	Mat new_image(src.size(), src.type());
	for (int y = 0; y < src.rows; y++)
	for (int x = 0; x < src.cols; x++) {
		int cluster_idx = labels.at<int>(y + x * src.rows, 0);
		new_image.at<Vec3b >(y, x)[0] = uchar (centers.at<float>(cluster_idx, 0));
		new_image.at<Vec3b >(y, x)[1] = uchar(centers.at<float>(cluster_idx, 1));
		new_image.at<Vec3b >(y, x)[2] = uchar(centers.at<float>(cluster_idx, 2));
	}
	//imshow("clustered image", new_image);
	new_image.copyTo(img_clust);
	// return new_image;
	//imwrite("clusterImg.jpg", img_clust);
	cvtColor(img_clust, k_img, CV_RGB2GRAY);
}


// callback function for open/close trackbar

void OpenClose(int, void*, int t) {
	// blur( src, src, Size(3,3) );
	int n = open_close_pos - max_iters;
	cout << "opc" << open_close_pos << endl;
	int an = n > 0 ? n : -n;
	cout << "an" << an << endl;

	Mat element = getStructuringElement(element_shape, Size(an * 2 + 1, an * 2 + 1), Point(an, an));
	if (n < 0)
		morphologyEx(img_clust, dst, CV_MOP_OPEN, element);
	else
		morphologyEx(img_clust, dst, CV_MOP_CLOSE, element);

	//imshow("Open/Close0", dst);
	cvtColor(dst, dst1, CV_BGR2GRAY);
	/// Convert image to gray and blur it
	blur(dst1, src_gray, Size(3, 3));

	// Reduce noise with a kernel 3x3
	blur(dst1, dst1, Size(3, 3));

	/// Canny detector

	cout << "lowthreshold :" << lowThreshold << endl;
	Canny(dst1, dst1, lowThreshold, ratio*lowThreshold, kernel_size);
	// imshow("Open/Close Canny", dst1);

	cout << "t" << t << endl;
	thresh_callback(0, 0);
}




/** @function main */
int main(int argc, char** argv) {

	/// Load source image and convert it to gray
	src = imread(argv[1], 1);
	
	cout << "argc" << argc << endl;

	if (argc != 2 || src.data) {
		cout << "No image Data" << endl;
		instr = "save";
	}
	saving = true;


	//for (int i = 0; i < 5; i++){
		FILE* fichier = NULL;

		fichier = fopen("/root/sharefolder/file.txt", "r");
		 if (fichier ==NULL)
    	 {
         	cout << "error" << endl;
         	exit(0);
    	 }
    	fscanf(fichier,"%d %d %d",&TH,&open_close_pos,&lowThreshold);
    	cout << TH << " " << endl;
    	fclose(fichier);
    	std::ostringstream sss1,sss2,sss3;
    	sss1 << TH;
    	sss2 << open_close_pos;
    	sss3 << lowThreshold;
		//TH = atoi(argv[3]);
		//open_close_pos = atoi(argv[4]);
		//lowThreshold = atoi(argv[5]);
	
		//V = std::to_string(TH);
		//V1 = std::to_string(open_close_pos);
		//V2 = std::to_string(lowThreshold);
		//folderName.clear();


		folderName = folderName + sss1.str() + "_" + sss2.str() + "_" + sss3.str();
		//sprintf(folderName, "%d", num);
		
		string folderCreateCommand = "mkdir " + folderName;
		system(folderCreateCommand.c_str());

		ss << folderName << "/" << "MRI_vertebrae.jpg";
		ss1 << folderName << "/" << "MRI_segmented_vertebrae.jpg";
		ss2 << folderName << "/" << "MRI_vertebrae_Curve.jpg";

		fullPath = ss.str();
		fullPath1 = ss1.str();
		fullPath2 = ss2.str();

		ss.str("");
		ss1.str("");
		ss2.str("");

		cout << " Saving " << saving << endl;
		tailleX = src.cols;
		tailleY = src.rows;

		cvtColor(src, g_img, CV_RGB2GRAY);
		src.copyTo(img_clust);
		src.copyTo(metas);

		Kmeans();
		cout << "TH :" << TH << endl;
		cout << "open_close_pos :" << open_close_pos << endl;
		cout << "lowThreshold :" << lowThreshold << endl;
		OpenClose(open_close_pos, 0, TH);
		waitKey();
	//}

	return (0);
}



void polynomialFitting(vector<RotatedRect> Data) {

	Mat curve;
	src.copyTo(curve); //Nombre de données
	int deg = 3;
	size_t nbData = Data.size();

	// cout << "Data size : " << nbData << endl;


	double* x = new double[nbData];
	double* y = new double[nbData];


	for (int i = 0; i < nbData; i++) {
		y[i] = Data[i].center.x;
		x[i] = Data[i].center.y;
		//    cout << "Data " << i << " : " << Data[i].center << endl;
	}


	if (nbData > 3) {


		//  double x[nbData], y[nbData]; // données
		double M, N, P, Q, R, S, T, U, V, W, C11, C12, C13, C14, C15, C16, C17, C18; //coefficients du système linéaire
		double Y1, Y2, Y3, Y4, Y5, Y6, Y7, Y8, Y9, Y10;


		// REGRESSION jusqu'à l'ordre 9
		//Calcul des coéfficients de la matrice
		M = N = P = Q = R = S = T = U = V = W = C11 = C12 = C13 = C14 = C15 = C16 = C17 = C18 = Y1 = Y2 = Y3 = Y4 = Y5 = Y6 = Y7 = Y8 = Y9 = Y10 = 0;
		for (int i = 0; i < nbData; ++i) {
			double x_carre = x[i] * x[i];
			double x_cube = x_carre * x[i];
			double x_quatre = x_cube * x[i];
			double x_cinq = x_quatre * x[i];
			double x_six = x_cinq * x[i];
			double x_sept = x_six * x[i];
			double x_huit = x_sept * x[i];
			double x_neuf = x_huit * x[i];
			double x_dix = x_neuf * x[i];
			double x_11 = x_dix * x[i];
			double x_12 = x_11 * x[i];
			double x_13 = x_12 * x[i];
			double x_14 = x_13 * x[i];
			double x_15 = x_14 * x[i];
			double x_16 = x_15 * x[i];
			double x_17 = x_16 * x[i];
			double x_18 = x_17 * x[i];
			M += x[i];
			N += x_carre;
			P += x_cube;
			Q += x_quatre;
			R += x_cinq;
			S += x_six;
			T += x_sept;
			U += x_huit;
			V += x_neuf;
			W += x_dix;
			C11 += x_11;
			C12 += x_12;
			C13 += x_13;
			C14 += x_14;
			C15 += x_15;
			C16 += x_16;
			C17 += x_17;
			C18 += x_18;

			Y1 += y[i];
			Y2 += x[i] * y[i];
			Y3 += x_carre * y[i];
			Y4 += x_cube * y[i];
			Y5 += x_quatre * y[i];
			Y6 += x_cinq * y[i];
			Y7 += x_six * y[i];
			Y8 += x_sept * y[i];
			Y9 += x_huit * y[i];
			Y10 += x_neuf * y[i];
		}



		// create a 3x3 double-precision identity matrix


		Mat X = (Mat_<double>(deg + 1, deg + 1) << nbData, M, N, P, M, N, P, Q, N, P, Q, R, P, Q, R, S); // 5th : nbData,M,N,P,Q,R,  M,N,P,Q,R,S,  N,P,Q,R,S,T,  P,Q,R,S,T,U,  Q,R,S,T,U,V,  R,S,T,U,V,W); // 4th : nbData,M,N,P,Q,  M,N,P,Q,R,  N,P,Q,R,S,  P,Q,R,S,T,  Q,R,S,T,U);//3rd :    //2nd : nbData,M,N,M,N,P,N,P,Q); 
		Mat Y = (Mat_<double>(deg + 1, 1) << Y1, Y2, Y3, Y4);
		Mat A = X.inv(DECOMP_LU) * Y;
		int dimA = A.dims;
		Mat Xinv = X.inv(DECOMP_LU);

		//
		//cout << "X = "<< endl << " "  << X << endl << endl; 
		// cout<<"determinant(X)="<<determinant(X)<<"\n";
		// cout << "Y = "<< endl << " "  << Y << endl << endl; 
		// cout << "A = "<< endl << " "  << A << endl << endl;
		//  cout << "Xinv = "<< endl << " "  << Xinv << endl << endl;

		//Coefficients du polynome


		a0 = A.at<double>(0, 0);
		a1 = A.at<double>(1, 0);
		a2 = A.at<double>(2, 0);
		a3 = A.at<double>(3, 0); //a3=A.at<double>(2,0);
		a4 = 0.0; //A.at<double>(4,0);
		a5 = 0.0; //A.at<double>(5,0);
		a6 = 0.0;
		a7 = 0.0;
		a8 = 0.0;
		a9 = 0.0;
		;
		//Dessin polynome

		double d = 0;
		beginCurve = MAX(int(Data[0].center.y - 15), 0);

		endCurve = int(Data[nbData - 1].center.y + 20);
		//    cout << "beginCurve " << beginCurve << endl;
		//    cout << "endCurve " << endCurve << endl;

		for (int c = beginCurve; c < endCurve && c < tailleY; c++) {

			d = a0 + (a1 * c) + (a2 * c * c) + (a3 * c * c * c);
			circle(curve, cvPoint(int(d), c), 2, CV_RGB(255, 0, 0), -1, CV_AA, 0);
		}

		for (int z = 0; z < nbData; z++) {

			int d = 0;
			int c = int(x[z]);
			d = int(a0 + (a1 * c) + (a2 * c * c) + (a3 * c * c * c));
			//   cout<<"disatnce point curve 1 "<< z<< "  est : "<<abs(y[z]-d)<<endl; 

		}

		for (int i = 0; i < nbData; i++) {
			circle(curve, Point(int(y[i]), int(x[i])), 3, CV_RGB(0, 255, 0), -1, CV_AA, 0);
		}

		//====================================
		//
		//====================================

		vector <point> data;
		vector <point> newdata;
		for (int i = 0; i < nbData; i++) {
			point p;
			p.y = float(y[i]);
			p.x = float(x[i]);
			p.active = true;
			int d = 0;
			int c = int(p.x);
			d = int(a0 + (a1 * c) + (a2 * c * c) + (a3 * c * c * c));
			//    cout<<"disatnce point "<< i<< "  est : "<<abs(p.y-d)<<endl; 
			if (abs(p.y - d) < 15) {
				data.push_back(p);
			}
		}
		//long nbdata2 = data.size();
		if (data.size() > 0) {
			size_t nbdata2 = data.size();
			if (nbdata2 > 3) {
				//On supprime les points trop loin de la courbe (distance 15)
				//===========================
				// REGRESSION jusqu'à l'ordre 9
				//Calcul des coéfficients de la matrice
				M = N = P = Q = R = S = T = U = V = W = C11 = C12 = C13 = C14 = C15 = C16 = C17 = C18 = Y1 = Y2 = Y3 = Y4 = Y5 = Y6 = Y7 = Y8 = Y9 = Y10 = 0;
				for (int i = 0; i < nbdata2; ++i) {
					double x_carre = data[i].x * data[i].x;
					double x_cube = x_carre * data[i].x;
					double x_quatre = x_cube * data[i].x;
					double x_cinq = x_quatre * data[i].x;
					double x_six = x_cinq * data[i].x;
					double x_sept = x_six * data[i].x;
					double x_huit = x_sept * data[i].x;
					double x_neuf = x_huit * data[i].x;
					double x_dix = x_neuf * data[i].x;
					double x_11 = x_dix * data[i].x;
					double x_12 = x_11 * data[i].x;
					double x_13 = x_12 * data[i].x;
					double x_14 = x_13 * data[i].x;
					double x_15 = x_14 * data[i].x;
					double x_16 = x_15 * data[i].x;
					double x_17 = x_16 * data[i].x;
					double x_18 = x_17 * data[i].x;
					M += x[i];
					N += x_carre;
					P += x_cube;
					Q += x_quatre;
					R += x_cinq;
					S += x_six;
					T += x_sept;
					U += x_huit;
					V += x_neuf;
					W += x_dix;
					C11 += x_11;
					C12 += x_12;
					C13 += x_13;
					C14 += x_14;
					C15 += x_15;
					C16 += x_16;
					C17 += x_17;
					C18 += x_18;

					Y1 += data[i].y;
					Y2 += x[i] * data[i].y;
					Y3 += x_carre * data[i].y;
					Y4 += x_cube * data[i].y;
					Y5 += x_quatre * data[i].y;
					Y6 += x_cinq * data[i].y;
					Y7 += x_six * data[i].y;
					Y8 += x_sept * data[i].y;
					Y9 += x_huit * data[i].y;
					Y10 += x_neuf * data[i].y;
				}



				// create a 3x3 double-precision identity matrix


				Mat XX = (Mat_<double>(deg + 1, deg + 1) << nbdata2, M, N, P, M, N, P, Q, N, P, Q, R, P, Q, R, S); // 5th : nbData,M,N,P,Q,R,  M,N,P,Q,R,S,  N,P,Q,R,S,T,  P,Q,R,S,T,U,  Q,R,S,T,U,V,  R,S,T,U,V,W); // 4th : nbData,M,N,P,Q,  M,N,P,Q,R,  N,P,Q,R,S,  P,Q,R,S,T,  Q,R,S,T,U);//3rd :    //2nd : nbData,M,N,M,N,P,N,P,Q); 
				Mat YY = (Mat_<double>(deg + 1, 1) << Y1, Y2, Y3, Y4);
				Mat AA = XX.inv(DECOMP_LU) * YY;
				int dimAA = AA.dims;
				Mat XXinv = XX.inv(DECOMP_LU);


				//Coefficients du polynome


				b0 = A.at<double>(0, 0);
				b1 = A.at<double>(1, 0);
				b2 = A.at<double>(2, 0);
				b3 = A.at<double>(3, 0); //a3=A.at<double>(2,0);
				b4 = 0.0; //A.at<double>(4,0);
				b5 = 0.0; //A.at<double>(5,0);
				b6 = 0.0;
				b7 = 0.0;
				b8 = 0.0;
				b9 = 0.0;
				;
				//Dessin polynome

				src.copyTo(curveNew);

				//GaussianBlur(curveNew, curveNew, Size(9, 9), 1, 1);
				// blur( curveNew, curveNew,Size(9,9), Point(-1,-1),BORDER_DEFAULT );
				double dd = 0;
				int beginCurve2 = MAX(int(data[0].y - 10), 10);

				int endCurve2 = MIN(int(data[nbdata2 - 1].y + 10), tailleY - 10);
				//    cout << "beginCurve " << beginCurve2 << endl;
				//    cout << "endCurve " << endCurve2 << endl;

				for (int c = beginCurve; c < endCurve && c < tailleY; c++) {

					dd = b0 + (b1 * c) + (b2 * c * c) + (b3 * c * c * c);
					circle(curveNew, cvPoint(int(dd), c), 2, CV_RGB(255, 0, 0), -1, CV_AA, 0);
				}

				for (int i = 0; i < nbdata2; i++) {
					circle(curveNew, Point(int(data[i].y), int(data[i].x)), 3, CV_RGB(0, 255, 0), -1, CV_AA, 0);
				}

				//==========
				//addWeighted(curveNew, 0.8, src, 0.99, 0.0, curveNew);
				if (saving)
				{
					//imwrite("MRI_vertebrae_Curve.jpg", curveNew);
					imwrite(fullPath, curveNew);
				}
					
				else cv::imshow("curveNew", curveNew);
			}
		}
		//Localisation des IVD
		//    for (int i = 0; i < nbData-1; i++)
		//             circle(curve, Point((y[i]+y[i+1])/2,(x[i]+x[i+1])/2), 2, CV_RGB(0,0, 255), -1,8,1);
		//  
	}
	addWeighted(curve, 0.8, src, 0.99, 0.0, curve);
	//imshow("Curve", curve);
	curve.release();
	Data.clear();
	//imshow("Contours", weighted);

}

void grayProfile() {


	Mat k_profile(300, 3 * tailleY, CV_8UC3, CV_RGB(255, 255, 255));
	Mat g_profile(300, 3 * tailleY, CV_8UC3, CV_RGB(255, 255, 255));


	int xc = 0;
	int y_bord = 30;
	// vector <int> g;

	vector <pointz> k_profile_vec;
	vector <pointz> g_profile_vec;


	GaussianBlur(g_img, g_img, Size(3, 3), 1, 1);

	for (int yc = y_bord; yc < tailleY - y_bord; yc++) {
		pointz elem;
		xc = poly(yc);

		elem.x = xc;
		elem.y = yc;

		elem.value = float((k_img.at<uchar>(yc, xc - 2) + k_img.at<uchar>(yc, xc - 1) + k_img.at<uchar>(yc, xc) + k_img.at<uchar>(yc, xc + 1) + k_img.at<uchar>(yc, xc + 2)) / 5);
		k_profile_vec.push_back(elem);

		//  g.push_back(k_profile.at<uchar>(yc, xc));

		elem.value = float((g_img.at<uchar>(yc, xc - 6) + g_img.at<uchar>(yc, xc - 5) + g_img.at<uchar>(yc, xc - 4) + g_img.at<uchar>(yc, xc - 3) + g_img.at<uchar>(yc, xc - 2) + g_img.at<uchar>(yc, xc - 1) + g_img.at<uchar>(yc, xc) + g_img.at<uchar>(yc, xc + 1) + g_img.at<uchar>(yc, xc + 2) + g_img.at<uchar>(yc, xc + 3) + g_img.at<uchar>(yc, xc + 4) + g_img.at<uchar>(yc, xc + 5) + g_img.at<uchar>(yc, xc + 6)) / 13);
		g_profile_vec.push_back(elem);


	}


	grid(k_profile, k_profile_vec);
	grid(g_profile, g_profile_vec);

	imshow("Kmeans Profile", k_profile);
	imshow("Gray Profile", g_profile);

	k_profile.release();
	g_profile.release();


}

void file(vector <int> gg) {
	ofstream fichier("signal.txt", ios::out | ios::trunc); // ouverture en écriture avec effacement du fichier ouvert

	if (fichier) // si l'ouverture a réussi
	{
		for (int i = 0; i < gg.size(); i++) {
			fichier << gg[i] << endl;
		}

		fichier.close(); // on referme le fichier
	}
	else // sinon
		cerr << "Erreur à l'ouverture !" << endl;
}

int poly(int yc) {
	return int(b0 + (b1 * yc) + (b2 * yc * yc) + (b3 * yc * yc * yc) + (b4 * yc * yc * yc * yc));

}



//Grid

void grid(Mat profile, vector <pointz> profile_vec) {
	int l = 50;
	int dist = 50;
	Scalar grid1 = Scalar(235, 235, 235);
	Scalar grid2 = Scalar(170, 170, 170);
	int midist = 25;

	int width = profile.size().width;
	int height = profile.size().height;

	for (int i = height - l; i >= 0; i -= midist)
		cv::line(profile, Point(l, i), Point(width, i), grid1);

	for (int i = l; i <= width - l; i += midist)
		cv::line(profile, Point(i, 0), Point(i, height - l), grid1);

	for (int i = height - l; i >= 0; i -= dist)
		cv::line(profile, Point(l, i), Point(width, i), grid2);

	for (int i = l; i <= width; i += dist)
		cv::line(profile, Point(i, 0), Point(i, height - l), grid2);


	for (int i = 1; i < profile_vec.size(); i++) {

		line(profile,
			Point(int(3 * (i - 1) + l), int(height - 2 * profile_vec[i - 1].value - l)),
			Point(int(3 * i + l), int(height - 2 * profile_vec[i].value - l)),
			CV_RGB(255, 0, 0), 1, CV_AA, 0);
	}

	char cc[2];

	for (int i = height - l; i > 0; i -= dist) {
		sprintf(cc, "%i", height - l - i);
		putText(profile, cc, Point(l / 4, i), 2, 0.5, CV_RGB(0, 0, 0), 1, 4);
	}


}




/*

void ivd(){

// file(g);  //exportation du signal






low point
for (int i = 1; i < g.size(); i++) {
if(g[i]<50 )

circle(profile,
Point(3 * i + l, height - 2 * g[i] - l),
1,
CV_RGB(0,0,255), 1, CV_AA);

}


//segment detection
vector <int> T;
int j = 0;
//  float midle;
T.push_back(0);
for (int i = 1; i < g.size(); i++) {


if (g[i] != g[i - 1]) {

T.push_back(i);
// cout<<"end point here : "<< i<<endl;
//             circle(profile,
//                Point(3 * i+l, height-2*g[i]-l),
//                2,
//                CV_RGB(0,0,0), 2, CV_AA);

}
}

// detection IVD

vector<int> centers;
Mat ivd;
src.copyTo(ivd);
for(int i=0;i<T.size();i++){
int midle=(T[i]+T[i+1])/2;
centers.push_back(midle);
if(g[midle]<50 ){
circle(profile,
Point(3*midle+l, height-2*g[midle]-l),
2,
CV_RGB(0,255,0), 2, CV_AA);
circle(ivd,
Point(profile_vec[midle].x,profile_vec[midle].y ),
2,
CV_RGB(0,0,255), 3, CV_AA);


}}
imshow("IVD",ivd);







int N=g.size();
Mat out(2*height, 3*N, CV_8UC3, CV_RGB(255, 255, 255));
double output[N];

output[0]=g[0]+110;
for (int i = 1; i < N; i++) {

output[i]=g[i]+g[i-1];
line(out,
Point(3 * (i - 1) + l, 2*height - 2 * output[i - 1] - l),
Point(3 * i + l, 2*height - 2 * output[i] - l),
CV_RGB(255, 0, 255), 1, CV_AA, 0);

}

imshow("Out", out);
imshow("Gray src",grayImg);
imshow("IVD Points", ivd);

}

*/